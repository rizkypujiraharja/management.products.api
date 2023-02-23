<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\InventoryReservation\InventoryReservationController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('api.modules.inventory-reservation.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'warehouse_id'
            ],
        ]);
    }
}
