<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\InventoryReservation\InventoryReservationController;

use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
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

        $response = $this->actingAs($user, 'api')->post(route('api.modules.inventory-reservation.store'), $params);

        $response->assertStatus(200);
    }
}
