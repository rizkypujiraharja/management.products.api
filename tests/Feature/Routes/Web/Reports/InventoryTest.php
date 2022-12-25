<?php

namespace Tests\Feature\Routes\Web\Reports;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 *
 */
class InventoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $uri = '/reports/inventory';

    /**
     * @var User
     */
    protected User $user;

    /**
     *
     */
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

        Warehouse::factory()->create();
        Product::factory()->create();

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    /** @test */
    public function test_admin_call()
    {
        $this->user->assignRole('admin');

        Warehouse::factory()->create();
        Product::factory()->create();

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }
}
