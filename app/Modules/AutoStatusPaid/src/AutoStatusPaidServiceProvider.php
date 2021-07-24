<?php

namespace App\Modules\AutoStatusPaid\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class AutoStatusPaidServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public string $module_name = 'Auto Paid Status';

    /**
     * @var string
     */
    public string $module_description = 'Automatically changes status from "processing" to "paid" '.
        'if order has been paid';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\ProcessingToPaidListener::class,
        ],
    ];
}
