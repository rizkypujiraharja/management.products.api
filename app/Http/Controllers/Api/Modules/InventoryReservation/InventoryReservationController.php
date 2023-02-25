<?php

namespace App\Http\Controllers\Api\Modules\InventoryReservation;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryReservationsConfigurationIndexRequest;
use App\Http\Requests\InventoryReservationsConfigurationUpdateRequest;
use App\Http\Resources\InventoryReservsationsConfigurationResource;
use App\Modules\InventoryReservations\src\Models\Configuration;

class InventoryReservationController extends Controller
{
    public function index(InventoryReservationsConfigurationIndexRequest $request)
    {
        $inventoryReservation = Configuration::first();

        return new InventoryReservsationsConfigurationResource($inventoryReservation);
    }

    public function update(InventoryReservationsConfigurationUpdateRequest $request, Configuration $inventoryReservation)
    {
        $inventoryReservation->update($request->validated());

        return new InventoryReservsationsConfigurationResource($inventoryReservation);
    }
}
