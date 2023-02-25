<?php

namespace Tests\Feature\Routes\Web\Admin\Settings\Modules;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 *
 */
class Inventory_reservationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $uri = '/admin/settings/modules/inventory-reservations';

    protected mixed $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_if_uri_set()
    {
        $this->assertNotEmpty($this->uri);
    }

    /** @test */
    public function test_guest_call()
    {
        $response = $this->get($this->uri);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_user_call()
    {
        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertForbidden();
    }

    /** @test */
    public function test_admin_call()
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertOk();
    }
}
