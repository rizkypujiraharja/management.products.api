<?php

namespace App\Modules\InventoryReservations\src;

use App\Events\HourlyEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Models\Warehouse;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\InventoryReservations\src\Models\Configuration;

/**
 * Class EventServiceProviderBase.
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Inventory Reservations';

    /**
     * @var string
     */
    public static string $module_description = 'Reserves stock for active orders.';

    /**
     * @var string
     */
    public static string $settings_link = '/admin/settings/modules/inventory-reservations';

    /**
     * @var bool
     */
    public static bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        HourlyEvent::class => [
            Listeners\HourlyEventListener::class,
        ],

        OrderProductUpdatedEvent::class => [
            Listeners\OrderProductUpdatedEventListener::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEventListener::class,
        ],

        OrderProductCreatedEvent::class => [
            Listeners\OrderProductCreatedEventListener::class,
        ],

        InventoryUpdatedEvent::class => [
            Listeners\InventoryUpdatedEventListener::class,
        ],
    ];

    public static function enableModule(): bool
    {
        if (! parent::enableModule()) {
            return false;
        }

        if (Configuration::query()->doesntExist()) {
            $warehouse = Warehouse::firstOrCreate(['code' => '999'], ['name' => '999']);

            Configuration::create([
                'warehouse_id' => $warehouse->id,
            ]);
        }

        return true;
    }

    public static function disabling(): bool
    {
        return false;
    }
}
