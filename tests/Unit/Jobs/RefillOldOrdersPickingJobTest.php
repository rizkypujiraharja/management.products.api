<?php

namespace Tests\Unit\Jobs;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\AutoStatusPicking\src\Jobs\RefillPickingIfEmptyJob;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefillOldOrdersPickingJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        /** @var Order $order */
        $order = Order::factory()->create([
            'order_placed_at' => Carbon::now()->subDays(100),
            'status_code'     => 'processing',
        ]);

        OrderProduct::factory()->create([
            'order_id' => $order->id,
        ]);

        $order->update(['status_code' => 'paid']);

        RefillPickingIfEmptyJob::dispatchNow();

        $this->assertDatabaseHas('orders', ['status_code' => 'picking']);
    }
}
