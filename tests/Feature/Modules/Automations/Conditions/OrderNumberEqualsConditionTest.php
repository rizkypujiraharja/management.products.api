<?php

namespace Tests\Feature\Modules\Automations\Conditions;

use App\Events\HourlyEvent;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\Conditions\OrderNumberEqualsOrderCondition;
use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Tests\TestCase;

class OrderNumberEqualsConditionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        OrderStatus::query()->forceDelete();
        Automation::query()->forceDelete();

        ray()->clearAll();


        $automation = factory(Automation::class)->create(['event_class' => 'App\Events\Order\ActiveOrderCheckEvent']);

        factory(Condition::class)->create([
            'automation_id'     => $automation->getKey(),
            'condition_class'   => OrderNumberEqualsOrderCondition::class,
            'condition_value'   => '123456'
        ]);

        factory(Action::class)->create([
            'automation_id'     => $automation->getKey(),
            'action_class'   => SetStatusCodeAction::class,
            'action_value'   => 'new_status_code'
        ]);

        $automation->update(['enabled' => true]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        factory(OrderStatus::class)->create(['code' => 'active', 'order_active' => true]);
        factory(Order::class)->create(['order_number' => '000000', 'status_code' => 'active']);
        factory(Order::class)->create(['order_number' => '123456', 'status_code' => 'active']);

        RunAutomationsOnActiveOrdersJob::dispatch();

        $this->assertDatabaseHas('orders', ['status_code' => 'new_status_code']);
    }
}
