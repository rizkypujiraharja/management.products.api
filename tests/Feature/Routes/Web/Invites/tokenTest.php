<?php

namespace Tests\Feature\Routes\Web\Invites;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_token_call_returns_ok()
    {
        $this->markTestIncomplete('This test was generated by "php artisan app:generate-api-routes-tests" call');
    }
}
