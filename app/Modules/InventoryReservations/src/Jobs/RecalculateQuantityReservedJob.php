<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Helpers\TemporaryTable;
use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Modules\InventoryReservations\src\Models\Configuration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class RecalculateQuantityReservedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->prepareTempTable('temp_table_totals');

        $this->incorrectInventoryRecordsQuery()
            ->get()
            ->each(function (Inventory $inventory) {
                Log::warning('Incorrect quantity_reserved detected', [
                    'sku'                        => $inventory->product->sku,
                    'quantity_reserved_expected' => $inventory['quantity_to_ship_sum'],
                    'quantity_reserved_actual'   => $inventory['quantity_reserved'],
                ]);

                $inventory->product->log('Updating quantity reserved');

                $inventory->update(['quantity_reserved' => $inventory['quantity_to_ship_sum']]);
            });
    }

    private function prepareTempTable($table_name): void
    {
        $baseQuery = OrderProduct::query()
            ->select([
                'orders_products.product_id',
                DB::raw('sum(orders_products.quantity_to_ship) as quantity_to_ship_sum')
            ])
            ->join('orders', 'orders.id', '=', 'orders_products.order_id')
            ->whereNotNull('orders_products.product_id')
            ->where('orders.is_active', '=', true)
            ->where('orders_products.quantity_to_ship', '>', '0')
            ->groupBy('orders_products.product_id');

        TemporaryTable::create($table_name, $baseQuery);
    }

    /**
     * @return Inventory|Builder
     */
    private function incorrectInventoryRecordsQuery()
    {
        $inventoryReservationWarehouseIdId = Configuration::first()->warehouse_id;

        return Inventory::query()
            ->with('product')
            ->select([
                'inventory.*',
                DB::raw('IFNULL(temp_table_totals.quantity_to_ship_sum, 0) as quantity_to_ship_sum'),
            ])
            ->leftJoin('temp_table_totals', 'inventory.product_id', '=', 'temp_table_totals.product_id')
            ->where('inventory.warehouse_id', '=', $inventoryReservationWarehouseIdId)
            ->whereRaw('inventory.quantity_reserved != IFNULL(temp_table_totals.quantity_to_ship_sum, 0)');
    }
}
