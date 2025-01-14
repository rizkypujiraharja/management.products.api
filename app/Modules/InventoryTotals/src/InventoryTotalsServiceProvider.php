<?php

namespace App\Modules\InventoryTotals\src;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class InventoryTotalsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Inventory Totals';

    /**
     * @var string
     */
    public static string $module_description = 'Tracks inventory totals for each product';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ProductCreatedEvent::class => [
            Listeners\ProductCreatedEventListener::class,
        ],

        InventoryUpdatedEvent::class => [
            Listeners\InventoryUpdatedEventListener::class,
        ],
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
