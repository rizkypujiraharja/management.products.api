<?php

namespace Tests\Feature\Modules\InventoryReservations\Jobs;

use App\Models\Inventory;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryReservations\src\EventServiceProviderBase;
use App\Modules\InventoryReservations\src\Jobs\RecalculateQuantityReservedJob;
use App\Modules\InventoryReservations\src\Models\ReservationWarehouse;
use App\User;
use Tests\TestCase;

/**
 * Class OrderCreatedTest.
 */
class RecalculateQuantityReservedJobTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');

        EventServiceProviderBase::enableModule();
    }

    /** @test */
    public function test_if_recalculates_correctly()
    {
        OrderStatus::factory()->create([
            'code'          => 'new',
            'name'          => 'new',
            'order_active'  => true,
        ]);

        $product = Product::factory()->create();

        $random_quantity = rand(1, 20);

        $data = [
            'order_number' => '1234',
            'status_code'  => 'new',
            'products'     => [
                [
                    'sku'      => $product->sku,
                    'name'     => $product->name,
                    'quantity' => $random_quantity,
                    'price'    => $product->price,
                ],
            ],
        ];

        $this->postJson('api/orders', $data)->assertOk();

        Inventory::query()->update(['quantity_reserved' => 0]);
        Product::query()->update(['quantity_reserved' => 0]);

        RecalculateQuantityReservedJob::dispatchNow();

        $this->assertDatabaseHas('inventory', ['quantity_reserved' => $random_quantity]);
        $this->assertDatabaseHas('products', ['quantity_reserved' => $random_quantity]);
    }

    /** @test */
    public function test_if_recalculates_correctly_if_0_reserved()
    {
        Product::factory()->create();

        $reservationWarehouseId = ReservationWarehouse::first()->warehouse_id;

        Inventory::query()->where(['warehouse_id' => $reservationWarehouseId])->update(['quantity_reserved' => 4]);

        RecalculateQuantityReservedJob::dispatchNow();

        $this->assertDatabaseHas('inventory', ['warehouse_id' => $reservationWarehouseId, 'quantity_reserved' => 0]);
        $this->assertDatabaseHas('products', ['quantity_reserved' => 0]);
    }
}
