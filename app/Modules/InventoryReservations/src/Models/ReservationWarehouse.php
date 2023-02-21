<?php

namespace App\Modules\InventoryReservations\src\Models;

use App\BaseModel;
use Illuminate\Support\Facades\Crypt;

/**
 * @property string $base_url
 * @property string $access_token
 * @property integer $magento_store_id
 * @property integer inventory_source_warehouse_tag_id
 */
class ReservationWarehouse extends BaseModel
{
    protected $table = 'modules_reservation_warehouses';

    protected $fillable = [
        'warehouse_id',
    ];
}
