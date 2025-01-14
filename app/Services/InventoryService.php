<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public static function sellProduct(Inventory $inventory, float $quantityDelta, string $description, string $unique_reference_id = null): InventoryMovement
    {
        DB::beginTransaction();
            /** @var InventoryMovement $inventoryMovement */
            $inventoryMovement = InventoryMovement::query()->create([
                'custom_unique_reference_id' => $unique_reference_id,
                'type' => 'sale',
//                'previous_movement_id' => $inventory->last_movement_id,
                'inventory_id' => $inventory->id,
                'product_id' => $inventory->product_id,
                'warehouse_id' => $inventory->warehouse_id,
                'quantity_before' => $inventory->quantity,
                'quantity_delta' => $quantityDelta,
                'quantity_after' => $inventory->quantity + $quantityDelta,
                'description' => $description,
            ]);

            $inventory->update([
                'last_movement_id' => $inventoryMovement->getKey(),
                'quantity' => $inventoryMovement->quantity_after
            ]);
        DB::commit();

        return $inventoryMovement;
    }

    public static function adjustQuantity(Inventory $inventory, float $quantityDelta, string $description, string $unique_reference_id = null): InventoryMovement
    {
        DB::beginTransaction();
            /** @var InventoryMovement $inventoryMovement */
            $inventoryMovement = InventoryMovement::query()->create([
                'custom_unique_reference_id' => $unique_reference_id,
                'type' => 'manual_adjustment',
//                'previous_movement_id' => $inventory->last_movement_id,
                'inventory_id' => $inventory->id,
                'product_id' => $inventory->product_id,
                'warehouse_id' => $inventory->warehouse_id,
                'quantity_before' => $inventory->quantity,
                'quantity_delta' => $quantityDelta,
                'quantity_after' => $inventory->quantity + $quantityDelta,
                'description' => $description,
            ]);

            $inventory->update([
                'last_movement_id' => $inventoryMovement->getKey(),
                'quantity' => $inventoryMovement->quantity_after
            ]);
        DB::commit();

        return $inventoryMovement;
    }
}
