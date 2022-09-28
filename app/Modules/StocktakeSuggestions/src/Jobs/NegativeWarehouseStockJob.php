<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use App\Models\Inventory;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class NegativeWarehouseStockJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    private int $warehouse_id;

    public function __construct(int $warehouse_id)
    {
        $this->warehouse_id = $warehouse_id;
    }

    public function handle(): bool
    {
        $reason = 'negative warehouse stock';
        $points = 20;


        $warehouses = Warehouse::withAllTags(['fulfilment'])->get('id');

        DB::statement('
            INSERT INTO stocktake_suggestions (inventory_id, points, reason, created_at, updated_at)
                SELECT DISTINCT inventory.id, ?, ?, now(), now()
                FROM inventory
                RIGHT JOIN inventory as inventory_fullfilment
                    ON inventory_fullfilment.product_id = inventory.product_id
                    AND inventory_fullfilment.warehouse_id IN (?)
                    AND inventory_fullfilment.quantity > 0
                WHERE inventory.warehouse_id = ?
                    AND inventory.quantity < 0
                    AND NOT EXISTS (
                        SELECT NULL
                        FROM stocktake_suggestions
                        WHERE stocktake_suggestions.inventory_id = inventory.id
                        AND stocktake_suggestions.reason = ?
                    )
                ORDER BY inventory.quantity ASC
                LIMIT 500
        ', [$points, $reason, $warehouses->pluck('id'), $this->warehouse_id, $reason]);

        return true;
    }
}