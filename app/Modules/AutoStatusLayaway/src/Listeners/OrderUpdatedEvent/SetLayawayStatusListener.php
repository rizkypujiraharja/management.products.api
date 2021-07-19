<?php


namespace App\Modules\AutoStatusLayaway\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoStatusLayaway\src\Jobs\SetLayawayStatusJob;

class SetLayawayStatusListener
{
    public function handle(OrderUpdatedEvent $event)
    {
        SetLayawayStatusJob::dispatchNow($event->getOrder());
    }
}
