<?php

namespace Tests\Browser;

use App\User;
use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsEventServiceProviderBase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class InventoryDashboardPageTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @throws Throwable
     */
    public function testExample()
    {
        InventoryReservationsEventServiceProviderBase::enableModule();
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                    ->visit('/reports/inventory-dashboard')
                    ->assertSee('INVENTORY DASHBOARD');
        });
    }
}
