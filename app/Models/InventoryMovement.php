<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @property int inventory_id
 * @property int product_id
 * @property int warehouse_id
 * @property float quantity_delta
 * @property float quantity_before
 * @property float quantity_after
 * @property string description
 * @property int user_id
 */
class InventoryMovement extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'inventory_id',
        'product_id',
        'warehouse_id',
        'quantity_delta',
        'quantity_before',
        'quantity_after',
        'description',
        'user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
