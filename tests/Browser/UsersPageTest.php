<?php

namespace Tests\Browser;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class UsersPageTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @throws Throwable
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $admin */
            $admin = User::factory()->create();
            $admin->assignRole('admin');

            $browser->loginAs($admin)
                ->disableFitOnFailure()
                ->visit('/admin/settings/users')
                ->pause(300)
                ->assertSee('Users')
                ->assertSee($admin->name);
        });
    }
}