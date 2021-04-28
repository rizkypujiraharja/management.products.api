<?php

namespace App\Listeners\Inventory;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\Inventory;
use App\Models\OrderProduct;

class InventoryUpdatedEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param InventoryUpdatedEvent $event
     * @return void
     */
    public function handle(InventoryUpdatedEvent $event)
    {
        $this->updateProductInventoryTotals($event);
    }

    /**
     * @param InventoryUpdatedEvent $event
     */
    public function updateProductInventoryTotals(InventoryUpdatedEvent $event)
    {
        $inventory = $event->getInventory();
        $product = $inventory->product;

        if ($product) {
            $product->recalculateQuantityTotals();
        }
    }
}
