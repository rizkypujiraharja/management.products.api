<?php

namespace Tests\Browser\Routes\Admin\Settings\Modules;

use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsEventServiceProviderBase;
use App\User;
use Exception;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class InventoryReservationsPageTest extends DuskTestCase
{
    private string $uri = '/admin/settings/modules/inventory-reservations';

    public function testBasics()
    {
        InventoryReservationsEventServiceProviderBase::enableModule();

        $this->basicAdminAccessTest($this->uri, true);
        $this->basicUserAccessTest($this->uri, false);
        $this->basicGuestAccessTest($this->uri);
    }

    public function testPage()
    {
        InventoryReservationsEventServiceProviderBase::enableModule();
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
            ->visit($this->uri)
            ->assertSee('Inventory Reservations');
        });
    }
}

