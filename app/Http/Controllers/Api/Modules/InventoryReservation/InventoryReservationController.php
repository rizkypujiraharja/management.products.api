<?php

namespace App\Http\Controllers\Api\Modules\InventoryReservation;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryReservationIndexRequest;
use App\Http\Requests\InventoryReservationStoreRequest;
use App\Http\Resources\InventoryReservationResource;
use App\Modules\InventoryReservations\src\Models\Configuration;

class InventoryReservationController extends Controller
{
    public function index(InventoryReservationIndexRequest $request)
    {
        $reservationWarehouse = Configuration::first();

        return new InventoryReservationResource($reservationWarehouse);
    }

    public function store(InventoryReservationStoreRequest $request)
    {
        $reservationWarehouse = Configuration::first();

        $reservationWarehouse->update($request->validated());

        return new InventoryReservationResource($reservationWarehouse);
    }
}
