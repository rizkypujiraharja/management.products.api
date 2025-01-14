<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\MagentoApi\src\Services\MagentoService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SyncCheckFailedProductsJob.
 */
class FetchStockItemsJob implements ShouldQueue
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
        MagentoProduct::query()
            ->whereNull('stock_items_fetched_at')
            ->orWhereNull('stock_items_raw_import')
            ->chunkById(100, function ($products) {
                collect($products)->each(function (MagentoProduct $product) {
                    try {
                        MagentoService::fetchInventory($product);
                    } catch (Exception $exception) {
                        report($exception);
                    }
                });
            });
    }
}
