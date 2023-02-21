<?php

namespace App\Modules\InventoryReservations\src\Models;

use App\BaseModel;

/**
 * @property integer $warehouse_id
 */
class ReservationWarehouse extends BaseModel
{
    protected $table = 'modules_reservation_warehouses';

    protected $fillable = [
        'warehouse_id',
    ];
}
