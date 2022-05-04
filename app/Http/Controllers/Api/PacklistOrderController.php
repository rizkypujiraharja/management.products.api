<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PacklistOrderIndexRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\Reports\src\Models\Report;
use Illuminate\Support\Facades\Auth;

/**
 * Class PacklistOrderController.
 */
class PacklistOrderController extends Controller
{
    /**
     * @param PacklistOrderIndexRequest $request
     *
     * @return OrderResource
     */
    public function index(PacklistOrderIndexRequest $request): OrderResource
    {
        $report = new Report();
        $report->fields = [
            'id'                                => 'order.id',
            'order_number'                      => 'order.order_number',
            'status'                            => 'order.status_code',
            'status_code'                       => 'order.status_code',
            'order_placed_at'                   => 'order.order_placed_at',
            'updated_at'                        => 'order.updated_at',
            'inventory_source_warehouse_id'     => 'inventory_source.warehouse_id',
            'inventory_source_warehouse_code'   => 'inventory_source.warehouse_code',
            'inventory_source_shelf_location'   => 'inventory_source.shelve_location',
        ];

        $report->baseQuery = OrderProduct::query()
            ->where('quantity_to_ship', '>', 0)
            ->leftJoin('orders as order', 'orders_products.order_id', '=', 'order.id')
            ->leftJoin('inventory as inventory_source', function ($join) {
                $join->on('inventory_source.product_id', '=', 'orders_products.product_id');
            })
            ->leftJoin('products', 'products.id', '=', 'orders_products.product_id')
            ->whereNull('packed_at')
            ->whereNull('packer_user_id');

        /** @var Order $order */
        $order = $report->queryBuilder()->firstOrFail();

        Order::query()
            ->where(['packer_user_id' => Auth::id()])
            ->whereNull('packed_at')
            ->get()
            ->each(function (Order $order) {
                $order->update(['packer_user_id' => null]);
            });

        $order = Order::where(['id' => $order->id])
            ->where(['updated_at' => $order->updated_at])
            ->whereNull('packer_user_id')
            ->first();

        $wasReserved = $order->update(['packer_user_id' => Auth::id()]);

        if (!$wasReserved) {
            $this->respondBadRequest('Order could not be reserved, try again');
        }

        $order->log('received order for packing');

        return new OrderResource(Order::findOrFail($order->id));
    }
}
