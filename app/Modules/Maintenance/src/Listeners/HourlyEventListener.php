<?php

namespace App\Modules\Maintenance\src\Listeners;

use App\Events\DailyEvent;
use App\Events\HourlyEvent;
use App\Modules\Maintenance\src\Jobs\EnsureProductSkusPresentInAliasesJob;
use App\Modules\Maintenance\src\Jobs\temp\StockInReservationWarehouseMonitorJob;

class HourlyEventListener
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     *
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        StockInReservationWarehouseMonitorJob::dispatch();
        EnsureProductSkusPresentInAliasesJob::dispatch();
    }
}
