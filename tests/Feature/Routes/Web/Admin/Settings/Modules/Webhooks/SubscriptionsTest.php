<?php

namespace Tests\Feature\Routes\Web\Admin\Settings\Modules\Webhooks;

use App\Modules\Webhooks\src\WebhooksServiceProviderBase;
use App\User;
use Tests\TestCase;

/**
 *
 */
class SubscriptionsTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/admin/settings/modules/webhooks/subscriptions';

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
        $this->user = factory(User::class)->create();
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
        WebhooksServiceProviderBase::disableModule();

        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertRedirect();
    }
}
