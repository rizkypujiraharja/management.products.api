<?php

namespace App\Listeners\Inventory;

use App\Events\Inventory\InventoryUpdatedEvent;

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
        $this->updateProductTotalQuantity($event);
        $this->updateProductQuantityReserved($event);
    }

    /**
     * Handle the event.
     *
     * @param InventoryUpdatedEvent $event
     * @return void
     */
    public function updateProductTotalQuantity(InventoryUpdatedEvent $event)
    {
        $event->getInventory()
            ->product()
            ->decrement(
                'quantity',
                $event->getInventory()->getOriginal('quantity')
            );

        $event->getInventory()
            ->product()
            ->increment(
                'quantity',
                $event->getInventory()->quantity
            );
    }

    /**
     * Handle the event.
     *
     * @param InventoryUpdatedEvent $event
     * @return void
     */
    public function updateProductQuantityReserved(InventoryUpdatedEvent $event)
    {
        $event->getInventory()
            ->product()
            ->decrement(
                'quantity_reserved',
                $event->getInventory()->getOriginal('quantity_reserved')
            );

        $event->getInventory()
            ->product()
            ->increment(
                'quantity_reserved',
                $event->getInventory()->quantity
            );
    }
}
