<?php

namespace Tests\Feature\Modules\InventoryReservations;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Modules\InventoryReservations\src\EventServiceProviderBase;
use App\Modules\InventoryReservations\src\Models\Configuration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        EventServiceProviderBase::enableModule();
    }

    public function test_if_reserves_correctly()
    {
        /** @var Order $product */
        $product = Product::factory()->create();

        /** @var OrderStatus $orderStatus */
        $orderStatus = OrderStatus::factory()->create(['order_active' => true]);

        /** @var Order $order */
        $order = Order::factory()->create(['status_code' => $orderStatus->code]);

        /** @var OrderProduct $orderProduct */
        $orderProduct = OrderProduct::factory()->create([
            'order_id' => $order->getKey(),
            'product_id' => $product->getKey()
        ]);

        $reservationWarehouseId = Configuration::first()->warehouse_id;
        $this->assertEquals(
            $orderProduct->quantity_to_ship,
            $orderProduct->product->inventory()
                ->where(['warehouse_id' => $reservationWarehouseId])
                ->first()->quantity_reserved
        );

        $orderProduct->quantity_ordered = 0;
        $orderProduct->save();

        $this->assertEquals(
            $orderProduct->quantity_to_ship,
            $orderProduct->product->inventory()
                ->where(['warehouse_id' => $reservationWarehouseId])
                ->first()->quantity_reserved
        );
    }
}
