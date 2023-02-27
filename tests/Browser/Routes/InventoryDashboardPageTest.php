<?php

namespace Tests\Browser\Routes;

use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsEventServiceProviderBase;
use Tests\DuskTestCase;
use Throwable;

class InventoryDashboardPageTest extends DuskTestCase
{
    private string $uri = '/inventory-dashboard';

    /**
     * @throws Throwable
     */
    public function testBasics()
    {
        InventoryReservationsEventServiceProviderBase::enableModule();

        $this->basicUserAccessTest($this->uri, true);
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicGuestAccessTest($this->uri);
    }

    public function testIncomplete()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}

