<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Automation\AutomationController;

use App\Modules\Automations\src\AutomationsServiceProvider;
use App\User;
use Tests\TestCase;

class GetConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');

        AutomationsServiceProvider::enableModule();
    }

    /** @test */
    public function test_get_config_call_returns_ok()
    {
        $response = $this->get(route('api.settings.module.automations.config'));

        ray($response->json());

        $response->assertOk();
        $response->assertJsonStructure([
            'description',
            'conditions' => [
                '*' => [
                    'class',
                    'description'
                ]
            ],
            'actions' => [
                '*' => [
                    'class',
                    'description'
                ]
            ]
        ]);
    }
}
