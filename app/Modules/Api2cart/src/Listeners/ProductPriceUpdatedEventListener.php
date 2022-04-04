<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Product\ProductPriceUpdatedEvent;
use App\Modules\Api2cart\src\Models\Api2cartConnection;

class ProductPriceUpdatedEventListener
{
    /**
     * Handle the event.
     *
     * @param ProductPriceUpdatedEvent $event
     *
     * @return void
     */
    public function handle(ProductPriceUpdatedEvent $event)
    {
        $product_price = $event->product_price;

        if ($product_price->product->doesNotHaveTags(['Available Online'])) {
            return;
        }

        if (Api2cartConnection::where(['pricing_source_warehouse_id' => $product_price->warehouse_id])->exists()) {
            activity()->withoutLogs(function () use ($product_price) {
                $product_price->product->attachTag('Not Synced');
            });
        }
    }
}