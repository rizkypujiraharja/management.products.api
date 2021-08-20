<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Modules\Webhooks\src\AwsSns;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Spatie\Activitylog\Models\Activity;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishOrdersWebhooksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        activity()->withoutLogs(function () {
            $awaiting_publish_tag = config('webhooks.tags.awaiting.name');

            $orders = Order::withAllTags($awaiting_publish_tag)
                ->with('orderProducts')
                ->get();

            $this->queueData(['orders_count' => $orders->count()]);

            $orders->each(function (Order $order) {
                $order->attachTag(config('webhooks.tags.publishing.name'));
                $order->detachTag(config('webhooks.tags.awaiting.name'));

                $orderResource = new OrderResource($order);
                if (!AwsSns::publish('orders_events', $orderResource->toJson())) {
                    $order->attachTag(config('webhooks.tags.awaiting.name'));
                }

                $order->detachTag(config('webhooks.tags.publishing.name'));
            });
        });
    }
}
