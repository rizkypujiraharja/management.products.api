<?php

namespace App\Http\Controllers\Api\Modules\ReservationWarehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationWarehouseIndexRequest;
use App\Http\Requests\ReservationWarehouseStoreRequest;
use App\Http\Resources\ReservationWarehouseResource;
use App\Modules\InventoryReservations\src\Models\ReservationWarehouse;

class ReservationWarehouseController extends Controller
{
    public function index(ReservationWarehouseIndexRequest $request)
    {
        $reservationWarehouse = ReservationWarehouse::first();

        return new ReservationWarehouseResource($reservationWarehouse);
    }

    public function store(ReservationWarehouseStoreRequest $request)
    {
        $reservationWarehouse = ReservationWarehouse::first();

        $reservationWarehouse->update($request->validated());

        return new ReservationWarehouseResource($reservationWarehouse);
    }
}
