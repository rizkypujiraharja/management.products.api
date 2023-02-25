<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\InventoryReservation\InventoryReservationController;

use App\Models\Warehouse;
use App\Modules\InventoryReservations\src\Models\Configuration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_store_call_returns_ok()
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $warehouse = Warehouse::firstOrCreate(['code' => '999'], ['name' => '999']);
        $params = [
            'warehouse_id' => $warehouse->id,
        ];
        $inventoryReservationsWarehouseId = Configuration::first()->warehouse_id;

        $response = $this->actingAs($user, 'api')->put(route('api.modules.inventory-reservations.configuration.update', $inventoryReservationsWarehouseId), $params);

        $response->assertStatus(200);
    }
}
