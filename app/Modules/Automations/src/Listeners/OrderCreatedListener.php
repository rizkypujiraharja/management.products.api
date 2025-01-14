<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Order\OrderCreatedEvent;
use App\Modules\Automations\src\Jobs\RunEnabledAutomationsOnSpecificOrderJob;

class OrderCreatedListener
{
    /**
     * @param OrderCreatedEvent $event
     */
    public function handle(OrderCreatedEvent $event)
    {
        RunEnabledAutomationsOnSpecificOrderJob::dispatchNow($event->order->getKey());
    }
}
