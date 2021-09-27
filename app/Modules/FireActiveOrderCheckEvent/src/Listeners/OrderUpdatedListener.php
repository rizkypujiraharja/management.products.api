<?php

namespace App\Modules\FireActiveOrderCheckEvent\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\FireActiveOrderCheckEvent\src\Jobs\FireActiveOrderCheckEventJob;

/**
 *
 */
class OrderUpdatedListener
{
    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     *
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        if ($event->order->is_editing) {
            return;
        }

        FireActiveOrderCheckEventJob::dispatch($event->order);
    }
}