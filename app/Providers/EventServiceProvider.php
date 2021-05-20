<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        // App
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Product
        \App\Events\Product\ProductCreatedEvent::class => [
            \App\Modules\Sns\src\Listeners\ProductCreatedEvent\PublishSnsNotificationListener::class,
        ],

        \App\Events\Product\ProductUpdatedEvent::class => [
            \App\Modules\Sns\src\Listeners\ProductUpdatedEvent\PublishSnsNotificationListener::class,
        ],

        // ProductPrice
        \App\Events\ProductPrice\ProductPriceUpdatedEvent::class => [
            \App\Modules\Api2cart\src\Listeners\ProductPriceUpdatedEvent\AddNotSyncedTagListener::class,
        ],

        // ProductTag
        \App\Events\Product\ProductTagAttachedEvent::class => [
            \App\Modules\Api2cart\src\Listeners\ProductTagAttachedEvent\SyncWhenOutOfStockAttachedListener::class,
//            \App\Modules\MagentoApi\src\Listeners\ProductTagAttachedEvent\SyncWhenOutOfStockAttachedListener::class,
        ],

        \App\Events\Product\ProductTagDetachedEvent::class => [
            \App\Modules\Api2cart\src\Listeners\ProductTagDetachedEvent\SyncWhenOutOfStockDetachedListener::class,
//            \App\Modules\MagentoApi\src\Listeners\ProductTagDetachedEvent\SyncWhenOutOfStockDetachedListener::class,
        ],

        // Order
        \App\Events\Order\OrderCreatedEvent::class => [
            \App\Listeners\Order\OrderCreatedEventListener::class,
            \App\Modules\Sns\src\Listeners\OrderCreatedEvent\PublishSnsNotificationListener::class,
        ],

        \App\Events\Order\OrderUpdatedEvent::class => [
            \App\Listeners\Order\OrderUpdatedEventListener::class,
            \App\Modules\InventoryReservations\src\Listeners\OrderUpdatedListener::class,
            \App\Modules\Sns\src\Listeners\OrderUpdatedEvent\PublishSnsNotificationListener::class,
        ],

        // OrderProduct
        \App\Events\OrderProduct\OrderProductCreatedEvent::class => [
            \App\Modules\InventoryReservations\src\Listeners\OrderProductCreatedListener::class,
        ],

        // Inventory
        \App\Events\Inventory\InventoryCreatedEvent::class => [
            \App\Modules\InventoryReservations\src\Listeners\InventoryCreatedListener::class,
        ],

        \App\Events\Inventory\InventoryUpdatedEvent::class => [
            \App\Modules\InventoryReservations\src\Listeners\InventoryUpdatedListener::class,
            \App\Modules\Api2cart\src\Listeners\InventoryUpdatedEvent\AddNotSyncedTagListener::class,
            \App\Modules\AutoTags\src\Listeners\InventoryUpdatedEvent\AttachOutOfStockTagListener::class,
            \App\Modules\AutoTags\src\Listeners\InventoryUpdatedEvent\DetachOutOfStockTagListener::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
