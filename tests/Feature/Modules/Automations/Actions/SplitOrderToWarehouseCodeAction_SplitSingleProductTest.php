<?php

namespace Tests\Feature\Modules\Automations\Actions;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Jobs\FireActiveOrderCheckEventForAllActiveOrdersJob;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Automations\src\Actions\Order\SplitOrderToWarehouseCodeAction;
use App\Modules\Automations\src\AutomationsServiceProvider;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SplitOrderToWarehouseCodeAction_SplitSingleProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_splitting_one_product_between_warehouses()
    {
        AutomationsServiceProvider::enableModule();

        $warehouses = factory(Warehouse::class, 3)->create();

        /** @var Product $product */
        $product = factory(Product::class)->create();


        $warehouses->each(function (Warehouse $warehouse) use ($product) {
            Inventory::updateOrCreate([
                'product_id' => $product->getKey(),
                'warehouse_id' => $warehouse->getKey(),
                'location_id' => $warehouse->code,
            ],[
                'quantity' => 1
            ]);
        });

        /** @var  $order */
        $order = factory(Order::class)->create(['status_code' => 'packing']);

        $orderProduct = new OrderProduct();
        $orderProduct->order_id = $order->getKey();
        $orderProduct->product_id = $product->getKey();
        $orderProduct->name_ordered = $product->name;
        $orderProduct->sku_ordered = $product->sku;
        $orderProduct->price = $product->price;
        $orderProduct->quantity_ordered = 3;
        $orderProduct->save();

        $warehouses->each(function (Warehouse $warehouse) {
            $status_code_name = 'packing_'.$warehouse->code;

            $automation = new Automation();
            $automation->enabled = false;
            $automation->name = 'packing to '.$status_code_name;
            $automation->event_class = ActiveOrderCheckEvent::class;
            $automation->save();

            $condition = new Condition();
            $condition->automation_id = $automation->getKey();
            $condition->condition_class = StatusCodeEqualsCondition::class;
            $condition->condition_value = 'packing';
            $condition->save();

            $action = new Action();
            $action->automation_id = $automation->getKey();
            $action->action_class = SplitOrderToWarehouseCodeAction::class;
            $action->action_value = $warehouse->code.',packing_web';
            $action->save();

            $automation->enabled = true;
            $automation->save();
        });

        AutomationService::runAllAutomations();

        FireActiveOrderCheckEventForAllActiveOrdersJob::dispatch();

        // we will have original order left + X new ones
        $this->assertEquals(4, Order::count());
        $this->assertEquals(6, OrderProduct::sum('quantity_ordered'));
        $this->assertEquals(3, OrderProduct::sum('quantity_split'));
    }
}
