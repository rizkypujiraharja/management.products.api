<?php

namespace Tests\Feature\Modules\Maintenance\Jobs;

use App\Jobs\RunHourlyJobs;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsEventServiceProviderBase;
use Tests\TestCase;

class RecalculateProductQuantityJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        InventoryReservationsEventServiceProviderBase::enableModule();

        Product::query()->forceDelete();
        Order::query()->forceDelete();
        OrderProduct::query()->forceDelete();

        $order = Order::factory()
            ->create(['status_code' => 'paid']);

        OrderProduct::factory()
            ->create([
                'order_id' => $order->id,
            ]);

        Product::query()->update([
            'quantity' => 0,
        ]);

        RunHourlyJobs::dispatchNow();

        $this->assertEquals(
            Product::query()->sum('quantity'),
            Inventory::query()->sum('quantity')
        );
    }
}
