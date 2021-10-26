<?php

namespace App\Http\Controllers\Api\Settings\Modules;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\RunAutomationRequest;
use App\Models\Order;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 */
class RunAutomationController extends Controller
{
    /**
     * @param RunAutomationRequest $request
     * @param int $automation_id
     * @return JsonResource
     */
    public function store(RunAutomationRequest $request, int $automation_id): JsonResource
    {
        /** @var Automation $automation */
        $automation = Automation::findOrFail($automation_id);

        Order::where(['is_active' => true])
            ->get()
            ->each(function (Order $order) use ($automation) {
                $event = new ActiveOrderCheckEvent($order);

                AutomationService::runAutomation($automation, $event);
            });

        return JsonResource::make($automation);
    }
}
