<?php

namespace App\Jobs\Maintenance;

use App\Listeners\Order\StatusChanged\PackingStatusesRules;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunPackingWarehouseRuleOnPaidOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = Order::where('status_code', 'paid')
            ->get();

        $rule = new PackingStatusesRules();

        foreach ($orders as $order) {
            $rule->checkStatusAndUpdate($order);
        }

        info('Ran PackingStatusesRules on "paid" orders ');
    }
}
