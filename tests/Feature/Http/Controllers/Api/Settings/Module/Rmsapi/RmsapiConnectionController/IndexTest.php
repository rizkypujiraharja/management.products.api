<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Rmsapi\RmsapiConnectionController;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('connections.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ]
            ]
        ]);
    }
}
