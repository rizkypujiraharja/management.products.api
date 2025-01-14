<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderComment;
use App\Models\OrderProduct;
use App\Models\OrderProductTotal;
use App\Models\Product;
use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiShippingImports;
use App\Services\InventoryService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ImportShippingsJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private RmsapiConnection $rmsapiConnection;

    private string $batch_uuid;
    private OrderProduct $orderProduct;

    public int $uniqueFor = 120;

    public function uniqueId(): string
    {
        return implode('-', [get_class($this), $this->rmsapiConnection->getKey()]);
    }

    public function __construct(int $rmsapiConnectionId)
    {
        $this->rmsapiConnection = RmsapiConnection::find($rmsapiConnectionId);
        $this->batch_uuid = Uuid::uuid4()->toString();
    }

    public function handle(): bool
    {
        $params = [
            'per_page'            => 500,
            'order_by'            => 'DBTimeStamp:asc',
            'ShippingCarrierName' => 'PM',
            'min:DBTimeStamp' => $this->rmsapiConnection->shippings_last_timestamp,
        ];

        try {
            $response = RmsapiClient::GET($this->rmsapiConnection, 'api/shippings', $params);

            $records = $response->getResult();

            Log::debug('RMSAPI Downloaded Shippings', ['count' => count($records), 'params' => $params]);

            if (empty($records)) {
                return true;
            }

            RmsapiShippingImports::query()->create([
                'connection_id' => $this->rmsapiConnection->id,
                'raw_import' => $response->getResult()
            ]);

            $this->importShippingRecords($records);
        } catch (GuzzleException $e) {
            Log::warning('RMSAPI Failed shippings fetch', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * @param array $records
     */
    public function importShippingRecords(array $records): void
    {
        collect($records)
            ->each(function ($shippingRecord) {
                DB::transaction(function () use ($shippingRecord) {
                    Log::debug('RMSAPI Importing shipping record', $shippingRecord);
                    $orderProduct = $this->createOrderProductFrom($shippingRecord);

                    $this->restockOriginForStockToBalance($orderProduct);

                    $this->rmsapiConnection->update(['shippings_last_timestamp' => $shippingRecord['DBTimeStamp']]);
                });
            });
    }

    /**
     * @param array $record
     * @return Order
     */
    private function firstOrCreateOrder(array $record): Order
    {
        /** @var Order $order */
        $order = Order::firstOrCreate([
            'order_number' => $this->rmsapiConnection->location_id. '-TRN-' . $record['TransactionNumber'],
        ], [
            'status_code' => 'imported_rms_shippings',
            'order_placed_at' => $record['ShippingDateCreated'],
            'shipping_method_code' => $record['ShippingServiceName'],
            'shipping_method_name' => $record['ShippingCarrierName'],
        ]);

        /** @var OrderAddress $shippingAddress */
        $shippingAddress = OrderAddress::updateOrCreate([
            'id' => $order->shipping_address_id
        ], [
            'company' => $record['Company'],
            'first_name' => $record['Name'],
            'last_name' => $record['Name'],
            'email' => $record['EmailAddress'],
            'address1' => $record['Address'],
            'address2' => $record['Address2'],
            'postcode' => $record['Zip'],
            'city' => $record['City'],
            'state_code' => $record['State'],
            'state_name' => $record['State'],
            'country_code' => empty(trim(' ')) ? 'IRL' : $record['Country'],
            'country_name' => empty(trim(' ')) ? 'Ireland' : $record['Country'],
            'phone' => $record['PhoneNumber'],
            'fax' => '',
            'website' => '',
            'region' => '',
        ]);

        $order->update(['shipping_address_id' => $shippingAddress->getKey()]);

        if (! $order->wasRecentlyCreated) {
            return $order;
        }

        if (! empty($record['TransactionComment'])) {
            OrderComment::create([
                'order_id' => $order->getKey(),
                'comment' => trim($record['TransactionComment']),
            ]);
        }

        $order->logActivity('Order imported from RMS API', [
            'warehouse_code' => $this->rmsapiConnection->location_id,
            'transaction_number' => $record['TransactionNumber'],
        ]);

        return $order;
    }

    /**
     * @param $shippingRecord
     * @return OrderProduct|null:
     */
    private function createOrderProductFrom($shippingRecord): ?OrderProduct
    {
        $uuid = $this->rmsapiConnection->location_id . '-shipping.id-' . $shippingRecord['ID'];

        $order = $this->firstOrCreateOrder($shippingRecord);

        $orderProduct = OrderProduct::query()->where(['custom_unique_reference_id' => $uuid])->first();

        if ($orderProduct) {
            $this->rmsapiConnection->update(['shippings_last_timestamp' => $shippingRecord['DBTimeStamp']]);
            return $orderProduct;
        }

        $product = Product::findBySKU($shippingRecord['ItemLookupCode']);

        /** @var OrderProduct $orderProduct */
        $orderProduct = OrderProduct::create([
            'custom_unique_reference_id' => $uuid,
            'order_id' => $order->getKey(),
            'product_id' => $product ? $product->getKey() : null,
            'sku_ordered' => $shippingRecord['ItemLookupCode'],
            'name_ordered' => $shippingRecord['ItemDescription'],
            'quantity_ordered' => $shippingRecord['TransactionEntryQuantity'],
            'price' => $shippingRecord['TransactionEntryPrice'],
        ]);

        if (! empty($record['TransactionEntryComment'])) {
            OrderComment::create([
                'order_id' => $order->getKey(),
                'comment' => trim($record['TransactionEntryComment']),
            ]);
        }

        $orderProductTotal = $order->orderProductsTotals ?? OrderProductTotal::query()->create([
            'order_id' => $order->getKey(),
        ]);

        $order->update([
            'total_shipping' => $shippingRecord['ShippingCharge'],
            'total' => $orderProductTotal->total_price + $shippingRecord['ShippingCharge'],
            'total_paid' => $orderProductTotal->total_price + $shippingRecord['ShippingCharge'],
        ]);

        return $orderProduct;
    }

    /**
     * When creating shipping in RMS, it automatically creates transaction and deducts stock
     * We not 100% sure where the stock is gonna be shipped from so
     * We will restock sold products and reserve from all stock until shipped
     * then transaction will be created in warehouse where product ships from
     *
     * @param OrderProduct $orderProduct
     */
    private function restockOriginForStockToBalance(OrderProduct $orderProduct): void
    {
        $inventoryRecord = Inventory::query()->where([
            'product_id' => $orderProduct->product_id,
            'warehouse_code' => $this->rmsapiConnection->location_id,
        ])
        ->first();

        $unique_reference_id = 'rmsapi_shipping_import-order_product_id-' . $orderProduct->getKey();

        if (InventoryMovement::where(['custom_unique_reference_id' => $unique_reference_id])->exists()) {
            return;
        }

        InventoryService::adjustQuantity(
            $inventoryRecord,
            $orderProduct->quantity_ordered,
            'rmsapi_shipping_import',
            $unique_reference_id
        );

        $inventoryRecord->product->log('Imported RMS shipping, restocking', [
            'warehouse_code' => $inventoryRecord->warehouse_code,
            'quantity' => $orderProduct->quantity_ordered,
        ]);
    }
}
