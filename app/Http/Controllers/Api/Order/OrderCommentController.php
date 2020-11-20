<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCommentStoreRequest;
use App\Http\Resources\OrderCommentResource;
use App\Models\OrderComment;
use App\Models\OrderShipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class OrderCommentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $pick = QueryBuilder::for(OrderShipment::class)
            ->allowedFilters([
            ])
            ->allowedIncludes([
            ])
            ->allowedSorts([
            ]);

        $per_page = $request->get('per_page', 10);

        return $pick->paginate($per_page)->appends($request->query());
    }

    public function store(OrderCommentStoreRequest $request)
    {
        $shipment = new OrderComment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return OrderCommentResource::collection(collect([$shipment]));
    }
}
