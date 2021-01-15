<?php

namespace App\Jobs\Modules\Api2cart;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Api2cart\src\Jobs\UpdateOrCreateProductJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductJobCopy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Product
     */
    private $product;

    /**
     * Create a new job instance.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = $this->product;
        Api2cartConnection::all()->each(function ($connection) use ($product) {
            $product_data = $this->getProductData($product, $connection);
            UpdateOrCreateProductJob::dispatch($connection->bridge_api_key, $product_data);
        });
    }

    /**
     * @param Product $product
     * @param $connection
     * @return array
     */
    private function getProductData(Product $product, $connection): array
    {
        $product_data = $this->getBasicData($product);

        if($connection->pricing_location_id) {
            $product_data = array_merge($product_data, $this->getPricingData($product, $connection->pricing_location_id));
        } else {
            Log::warning('Pricing source not specified', ['connection ' => $connection->getAttributes(), 'product' => $product]);
        }

        if($connection->inventory_location_id) {
            $product_data = array_merge($product_data, $this->getInventoryData($product, $connection->inventory_location_id));
        } else {
            Log::warning('Inventory source not specified', ['connection ' => $connection->getAttributes(), 'product' => $product]);
        }

        if($connection->magento_store_id) {
            $product_data = array_merge($product_data, $this->getMagentoStoreId($connection));
        } else {
            Log::warning('Destination Magento Store ID not specified', ['connection ' => $connection->getAttributes(), 'product' => $product]);
        }

        return $product_data;
    }

    /**
     * @param Product $product
     * @return array
     */
    private function getBasicData(Product $product): array
    {
        return [
            'product_id' => $product->getKey(),
            'sku' => $product->sku
        ];
    }

    /**
     * @param Product $product
     * @param int $location_id
     * @return array
     */
    private function getPricingData(Product $product, int $location_id): array
    {
        $attributes = [
            'product_id' => $product->getKey(),
            'location_id' => $location_id
        ];

        $productPrice = ProductPrice::query()->where($attributes)->first();

        if(!$productPrice) {
            Log::warning('Pricing data not found', $attributes);
            return [];
        }

        return [
            'price' => $productPrice->price,
            'special_price' => $productPrice->sale_price,
            'sprice_create' => $this->formatDateForApi2cart($productPrice->sale_price_start_date),
            'sprice_expire' => $this->formatDateForApi2cart($productPrice->sale_price_end_date),
        ];
    }

    /**
     * @param Product $product
     * @param int $location_id
     * @return array
     */
    private function getInventoryData(Product $product, int $location_id): array
    {
        $attributes = [
            'product_id' => $product->getKey(),
            'location_id' => $location_id
        ];

        $productInventory = Inventory::query()->where($attributes)->first();

        if(empty($productInventory)) {
            Log::warning('Inventory data not found', $attributes);
            return [];
        }

        return [
            'quantity' => $productInventory->quantity_available ?? 0,
            'in_stock' => $productInventory->quantity_available > 0 ? "True" : "False",
        ];
    }

    /**
     * @param $date
     * @return string
     */
    public function formatDateForApi2cart($date): string
    {
        $carbon_date = new Carbon( $date ?? '2000-01-01 00:00:00');

        if ($carbon_date->year < 2000) {
            return '2000-01-01 00:00:00';
        }

        return $date;
    }

    /**
     * @param Api2cartConnection $connection
     * @return array
     */
    private function getMagentoStoreId(Api2cartConnection $connection): array
    {
        if(empty($connection->magento_store_id)) {
            Log::warning('Magento store id not specified!', $connection->getAttributes());
        }

        return [
            'store_id' => $connection->magento_store_id
        ];
    }
}