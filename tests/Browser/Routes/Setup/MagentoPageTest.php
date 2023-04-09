<?php

namespace Tests\Browser\Routes\Setup;

use App\User;
use Exception;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class MagentoPageTest extends DuskTestCase
{
    private string $uri = '/setup/magento';

    public function testBasics()
    {
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicUserAccessTest($this->uri, false);
        $this->basicGuestAccessTest($this->uri);
    }

    public function testPage()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit($this->uri)
                ->assertSee('Setup Magento API');
        });
    }
}

