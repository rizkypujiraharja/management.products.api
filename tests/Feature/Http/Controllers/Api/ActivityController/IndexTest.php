<?php

namespace Tests\Feature\Http\Controllers\Api\ActivityController;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('activities.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id'
                ],
            ],
        ]);
    }
}
