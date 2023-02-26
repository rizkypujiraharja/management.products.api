<?php

namespace Tests\Feature\Modules\SystemHeartbeats;

use App\Events\DailyEvent;
use App\Events\Every10minEvent;
use App\Events\HourlyEvent;
use App\Models\Heartbeat;
use App\Modules\SystemHeartbeats\src\Listeners\DailyEventListener;
use App\Modules\SystemHeartbeats\src\Listeners\Every10minEventListener;
use App\Modules\SystemHeartbeats\src\Listeners\HourlyEventListener;
use App\Modules\SystemHeartbeats\src\SystemHeartbeatsServiceProvider;
use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsEventServiceProviderBase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        SystemHeartbeatsServiceProvider::enableModule();
        InventoryReservationsEventServiceProviderBase::enableModule();

        Heartbeat::query()->forceDelete();
    }

    /** @test */
    public function test_every10minEvent_heartbeat()
    {
        Every10minEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => Every10minEventListener::class
        ]);
    }

        /** @test */
    public function test_hourlyEvent_heartbeat()
    {
        HourlyEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => HourlyEventListener::class
        ]);
    }

    /** @test */
    public function test_dailyEvent_heartbeat()
    {
        DailyEvent::dispatch();

        $this->assertDatabaseHas('heartbeats', [
            'code' => DailyEventListener::class
        ]);
    }
}
