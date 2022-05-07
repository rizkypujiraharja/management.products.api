<?php

namespace Tests\Feature\Modules\Maintenance;

use App\Events\DailyEvent;
use App\Modules\Maintenance\src\EventServiceProviderBase;
use App\Modules\Maintenance\src\Jobs\Products\EnsureAllInventoryRecordsExistsJob;
use App\Modules\Maintenance\src\Jobs\Products\EnsureAllProductPriceRecordsExistsJob;
use App\Modules\Maintenance\src\Jobs\Products\FixQuantityAvailableJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_module_basic_functionality()
    {
        EventServiceProviderBase::enableModule();

        Bus::fake();

        DailyEvent::dispatch();

        Bus::assertDispatched(EnsureAllInventoryRecordsExistsJob::class);
        Bus::assertDispatched(EnsureAllProductPriceRecordsExistsJob::class);
        Bus::assertDispatched(FixQuantityAvailableJob::class);
    }
}
