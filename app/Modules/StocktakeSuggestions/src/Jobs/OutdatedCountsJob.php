<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class OutdatedCountsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $warehouse_id;

    public function __construct(int $warehouse_id)
    {
        $this->warehouse_id = $warehouse_id;
    }

    public function handle(): bool
    {
        if (now()->month !== 1) {
            return true;
        }

        $reason = 'never counted';
        $points = 1;

        $this->insertNewSuggestions($this->warehouse_id, $reason, $points);
        $this->deleteIncorrectSuggestions($this->warehouse_id, $reason);

        return true;
    }

    /**
     * @param int $points
     * @param string $reason
     * @param int $warehouse_id
     */
    private function insertNewSuggestions(int $warehouse_id, string $reason, int $points): void
    {
        DB::statement("
            INSERT INTO stocktake_suggestions (inventory_id, product_id, warehouse_id, points, reason, created_at, updated_at)
            SELECT id, product_id, warehouse_id, ? , ?, NOW(), NOW()
            FROM inventory
            WHERE warehouse_id = ?
                AND quantity != 0
                AND (
                    last_counted_at IS NULL
                    OR last_counted_at < NOW() - INTERVAL 12 MONTH
                    OR last_counted_at < '" . now()->startOfMonth()->format('Y-m-d H:i:s') ."'
                )
                AND (
                    first_received_at IS NULL
                    OR first_received_at < '" . now()->startOfMonth()->format('Y-m-d H:i:s') ."'
                )
                AND NOT EXISTS (
                    SELECT NULL
                    FROM stocktake_suggestions
                    WHERE stocktake_suggestions.inventory_id = inventory.id
                    AND stocktake_suggestions.reason = ?
                )
        ", [$points, $reason, $warehouse_id, $reason]);
    }

    /**
     * @param int $warehouse_id
     * @param string $reason
     */
    private function deleteIncorrectSuggestions(int $warehouse_id, string $reason): void
    {
        DB::statement('
            DELETE stocktake_suggestions
            FROM stocktake_suggestions
            INNER JOIN inventory
                ON inventory.id = stocktake_suggestions.inventory_id

            WHERE stocktake_suggestions.warehouse_id = ?
            AND stocktake_suggestions.reason = ?
            AND inventory.quantity = 0
        ', [$warehouse_id, $reason]);
    }
}
