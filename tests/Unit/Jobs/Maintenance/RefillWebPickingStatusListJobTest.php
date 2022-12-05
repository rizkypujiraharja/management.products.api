<?php

namespace Tests\Unit\Jobs\Maintenance;

use App\Events\HourlyEvent;
use App\Models\AutoStatusPickingConfiguration;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Modules\AutoStatusPicking\src\AutoStatusPickingServiceProvider;
use Tests\TestCase;

class RefillWebPickingStatusListJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        AutoStatusPickingServiceProvider::enableModule();

        Product::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();

        OrderStatus::factory()->create(['code' => 'paid']);

        Product::factory()->count(30)->create();

        Order::factory()->count(150)
            ->with('orderProducts', 2)
            ->create(['status_code' => 'paid']);

        HourlyEvent::dispatch();

        $this->assertEquals(
            AutoStatusPickingConfiguration::firstOrCreate([], [])->max_batch_size,
            AutoStatusPickingConfiguration::firstOrCreate([], [])->current_count_with_status,
        );
    }
}
