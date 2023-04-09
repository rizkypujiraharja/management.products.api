<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\MagentoApi\MagentoApiConnectionController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetupTest extends TestCase
{
    /** @test */
    public function test_success_magento_setup()
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user, 'api')->json('post', route('api.modules.magento-api.connections.setup'), [
            'base_url'                          => 'https://magento2.test',
            'magento_store_id'                  => 123456,
        ]);

        $response->assertSuccessful();
    }

    /** @test */
    public function test_failing_magento_setup()
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user, 'api')->json('post', route('api.modules.magento-api.connections.store'), []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'base_url',
            'magento_store_id',
        ]);
    }
}
