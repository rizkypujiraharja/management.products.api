<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateImportedSalesRecordsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): bool
    {
        $this->updateWarehouseIds();

        $this->updateProductIds();

        $this->releaseTimesOutSalesRecords();

        return true;
    }

    private function updateWarehouseIds(): void
    {
        retry(3, function () {
            DB::statement('
                UPDATE modules_rmsapi_sales_imports
                LEFT JOIN modules_rmsapi_connections
                  ON modules_rmsapi_connections.id = modules_rmsapi_sales_imports.connection_id

                SET modules_rmsapi_sales_imports.warehouse_id = modules_rmsapi_connections.warehouse_id

                WHERE modules_rmsapi_sales_imports.warehouse_id IS NULL
            ');
        }, 1000);
    }

    private function updateProductIds(): void
    {
        retry(3, function () {
            DB::statement('
                UPDATE modules_rmsapi_sales_imports
                LEFT JOIN products_aliases
                  ON modules_rmsapi_sales_imports.sku = products_aliases.alias

                SET modules_rmsapi_sales_imports.product_id = products_aliases.product_id

                WHERE modules_rmsapi_sales_imports.product_id IS NULL
            ');
        }, 1000);
    }

    private function releaseTimesOutSalesRecords(): void
    {
        RmsapiSaleImport::query()
            ->whereNull('processed_at')
            ->where('reserved_at', '<', now()->subMinutes(5))
            ->update(['reserved_at' => null]);
    }
}
