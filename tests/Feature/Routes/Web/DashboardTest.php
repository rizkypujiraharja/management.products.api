<?php

namespace Tests\Feature\Routes\Web;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user, 'web');
    }

    /** @test */
    public function test_dashboard_call_returns_ok()
    {
        $this->markTestIncomplete('This test was generated by "php artisan app:generate-routes-tests" call');

//        $response = $this->get('');
//
//        $response->assertSuccessful();
    }
}
