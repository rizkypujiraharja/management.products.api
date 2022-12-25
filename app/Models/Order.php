<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\BaseModel;
use App\Traits\HasTagsTrait;
use App\Traits\LogsActivityTrait;
use App\User;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Tags\Tag;

/**
 * App\Models\Order.
 *
 * @property array            $raw_import
 *
 * @property int              $id
 * @property string           $order_number
 * @property string           $status_code
 * @property string           $label_template
 * @property string           $shipping_method_code
 * @property string           $shipping_method_name
 *
 * @property bool             $is_active
 * @property bool             is_on_hold
 *
 * @property int|null         $packer_user_id
 * @property int|null         $shipping_address_id
 *
 * @property int              product_line_count
 * @property float            $total_products
 * @property float            $total_shipping
 * @property float            $total
 * @property float            $total_discounts
 * @property float            $total_paid
 * @property float            $total_quantity_ordered
 * @property float            $total_quantity_to_ship
 *
 * @property Carbon|null      $picked_at
 * @property Carbon|null      $packed_at
 * @property Carbon|null      $order_placed_at
 * @property Carbon|null      $order_closed_at
 * @property Carbon|null      $deleted_at
 * @property Carbon|null      $created_at
 * @property Carbon|null      $updated_at
 *
 * @property-read boolean     $is_packed
 * @property-read boolean     $is_paid
 * @property-read boolean     $is_not_paid
 * @property-read boolean     $is_picked
 * @property-read boolean     $isPaid
 * @property-read boolean     $isNotPaid
 *
 * @property-read OrderProductTotal orderProductsTotals
 * @property-read OrderStatus $order_status
 * @property-read User|null   $packer
 * @property-read Collection|OrderComment[] $orderComments
 * @property-read int|null $order_comments_count
 * @property-read Collection|OrderProduct[] $orderProducts
 * @property-read int|null $order_products_count
 * @property-read HasMany $orderShipments
 * @property-read int|null $order_shipments_count
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Collection|Packlist[] $packlist
 * @property-read int|null $packlist_count
 *
 * @property-read OrderAddress|null $shippingAddress
 *
 * @method static Builder|Order addInventorySource($inventory_location_id)
 * @method static Builder|Order hasPacker($expected)
 * @method static Builder|Order isPacked($value)
 * @method static Builder|Order isPacking($is_packing)
 * @method static Builder|Order isPicked($expected)
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order where($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereDeletedAt($value)
 * @method static Builder|Order whereHasText($text)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereIsNotPicked()
 * @method static Builder|Order whereIsPicked()
 * @method static Builder|Order whereOrderClosedAt($value)
 * @method static Builder|Order whereOrderNumber($value)
 * @method static Builder|Order whereOrderPlacedAt($value)
 * @method static Builder|Order wherePackedAt($value)
 * @method static Builder|Order wherePackerUserId($value)
 * @method static Builder|Order wherePickedAt($value)
 * @method static Builder|Order whereProductLineCount($value)
 * @method static Builder|Order whereRawImport($value)
 * @method static Builder|Order whereShippingAddressId($value)
 * @method static Builder|Order whereStatusCode($value)
 * @method static Builder|Order whereTotal($value)
 * @method static Builder|Order whereTotalPaid($value)
 * @method static Builder|Order whereTotalQuantityOrdered($value)
 * @method static Builder|Order whereUpdatedAt($value)
 *
 * @property-read int $age_in_days
 * @property OrderStatus orderStatus
 * @property Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property bool is_editing
 *
 * @method static Builder|Order hasTags($tags)
 * @method static Builder|Order whereIsActive()
 * @method static Builder|Order packedBetween($fromDateTime, $toDateTime)
 * @method static Builder|Order whereAgeInDays($age)
 * @method static Builder|Order withAllTags($tags, $type = null)
 * @method static Builder|Order withAllTagsOfAnyType($tags)
 * @method static Builder|Order withAnyTags($tags, $type = null)
 * @method static Builder|Order withAnyTagsOfAnyType($tags)
 * @method static Builder|Order withoutAllTags($tags, $type = null)
 *
 * @mixin Eloquent
 */
class Order extends BaseModel
{
    use HasFactory;

    use LogsActivityTrait;
    use HasTagsTrait;

    protected $fillable = [
        'order_number',
        'picked_at',
        'packed_at',
        'label_template',
        'shipping_number',
        'is_active',
        'is_on_hold',
        'is_editing',
        'shipping_method_code',
        'shipping_method_name',
        'shipping_address_id',
        'is_packed',
        'order_placed_at',
        'order_closed_at',
        'status_code',
        'packer_user_id',
        'total',
        'total_products',
        'total_shipping',
        'total_discounts',
        'total_paid',
    ];

    /**
     * @var array|string[]
     */
    protected static array $logAttributes = [
        'status_code',
        'label_template',
        'packer_user_id',
    ];

    protected $casts = [
        'picked_at' => 'datetime',
        'packed_at' => 'datetime',
        'order_placed_at' => 'datetime',
        'order_closed_at' => 'datetime',
        'is_active'         => 'boolean',
        'is_on_hold'        => 'boolean',
        'is_editing'        => 'boolean',
        'total'             => 'float',
        'total_products'    => 'float',
        'total_shipping'    => 'float',
        'total_paid'        => 'float',
        'total_discounts'   => 'float',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'status_code'   => 'new',
        'is_active'     => 1,
        'is_editing'    => 0,
    ];

    protected $appends = [
        'is_picked',
        'is_packed',
        'age_in_days',
    ];
    /**
     * @return Builder
     */
    public static function active(): Builder
    {
        return self::query()->where(['is_active' => true]);
    }

    /**
     * @return Builder
     */
    public static function placedInLast28DaysOrActive(): Builder
    {
        return self::query()->where(function (Builder $query) {
            return $query->where(['is_active' => true])
                ->orWhereBetween('created_at', [now()->subDays(28), now()->subMinute()]);
        });
    }

    /**
     * @return bool
     */
    public function lockForEditing(): bool
    {
        // this should be used in places where order has to be updated by only single action
        // for example multiple jobs can be run at the same time but only one at a time should be allowed
        // successful update means order is not currently updating by any other action
        // when editing finished, you should unlock
        $recordsUpdated = Order::where([
                'id' => $this->getKey(),
                'is_editing' => false
            ])
            ->update(['is_editing' => true]);

        if ($recordsUpdated !== 1) {
            return false;
        }

        $this->is_editing = true;
        return true;
    }

    /**
     * @return bool
     */
    public function unlockFromEditing(): bool
    {
        $recordsUpdated = Order::whereId([$this->getKey()])->update(['is_editing' => false]);

        if ($recordsUpdated !== 1) {
            return false;
        }

        $this->is_editing = false;
        return true;
    }
//
//    /**
//     * @return OrderStatus
//     */
//    public function getOrderStatusAttribute(): OrderStatus
//    {
//        return $this->orderStatus()->first;
//    }

//    /**
//     * @return OrderStatus
//     */
//    public function orderStatus(): OrderStatus
//    {
//
//        return OrderStatus::firstOrCreate([
//            'code' => $this->status_code
//        ], [
//            'name' => $this->status_code,
//        ]);
//    }


    /**
     * @return OrderStatus
     */
    public function getPreviousOrderStatus(): OrderStatus
    {
        return OrderStatus::whereCode($this->getOriginal('status_code'))->first();
    }

    public function isOpen(): bool
    {
        return $this->order_closed_at === null;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return !$this->isOpen();
    }

    /**
     * @param $query
     * @param $text
     *
     * @return Builder|mixed
     */
    public function scopeWhereHasText($query, $text)
    {
        return $query->where('order_number', 'like', '%'.$text.'%')
            ->orWhere('status_code', '=', $text)
            ->orWhereHas('orderShipments', function ($query) use ($text) {
                return $query->where('shipping_number', 'like', '%'.$text.'%');
            });
    }

    /**
     * @return int
     */
    public function getAgeInDaysAttribute(): int
    {
        return Carbon::now()->ceilDay()->diffInDays($this->order_placed_at);
    }

    /**
     * @param mixed   $query
     * @param string  $fromDateTime
     * @param string  $toDateTime
     *
     * @return mixed
     */
    public function scopePackedBetween($query, string $fromDateTime, string $toDateTime)
    {
        try {
            $dates = [
                Carbon::parse($fromDateTime),
                Carbon::parse($toDateTime),
            ];
        } catch (Exception $exception) {
            $dates = [
                Carbon::today(),
                Carbon::now(),
            ];
        }

        return $query->whereBetween('packed_at', $dates);
    }

    /**
     * @param mixed $query
     * @param int   $age
     *
     * @return Builder|mixed
     */
    public function scopeWhereAgeInDays($query, int $age)
    {
        return $query->whereBetween('order_placed_at', [
            Carbon::now()->subDays($age)->startOfDay(),
            Carbon::now()->subDays($age)->endOfDay(),
        ]);
    }

    /**
     * @param mixed $query
     * @param int $warehouse_id
     * @return mixed
     */
    public function scopeAddInventorySource($query, int $warehouse_id)
    {
        $source_inventory = OrderProduct::query()
            ->select([
                'order_id as order_id',
                DB::raw('min(shelve_location) as min_shelf_location'),
                DB::raw('max(shelve_location) as max_shelf_location'),
            ])
            ->leftJoin('inventory', function ($join) use ($warehouse_id) {
                $join->on('orders_products.product_id', '=', 'inventory.product_id');
                $join->where('inventory.warehouse_id', '=', $warehouse_id);
            })
            ->groupBy('orders_products.order_id')
            ->toBase();

        return $query->leftJoinSub($source_inventory, 'inventory_source', function ($join) {
            $join->on('orders.id', '=', 'inventory_source.order_id');
        });
    }

    /**
     * @return bool
     */
    public function getIsPaidAttribute(): bool
    {
        return ($this->total_paid > 0) && ($this->total_paid >= $this->total);
    }

    /**
     * @return bool
     */
    public function getIsNotPaidAttribute(): bool
    {
        return !$this->isPaid;
    }

    /**
     * @param string $expected
     *
     * @return bool
     */
    public function isNotStatusCode(string $expected): bool
    {
        return !$this->isStatusCode($expected);
    }

    /**
     * @param string $expected
     *
     * @return bool
     */
    public function isStatusCode(string $expected): bool
    {
        return $this->getAttribute('status_code') === $expected;
    }

    /**
     * @param array $statusCodes
     *
     * @return bool
     */
    public function isStatusCodeNotIn(array $statusCodes)
    {
        return !$this->isStatusCodeIn($statusCodes);
    }

    /**
     * @param array $statusCodes
     *
     * @return bool
     */
    public function isStatusCodeIn(array $statusCodes)
    {
        $statusCode = $this->getAttribute('status_code');

        return array_search($statusCode, $statusCodes) > -1;
    }

    /**
     * @param mixed $query
     * @param bool $expected
     *
     * @return mixed
     */
    public function scopeHasPacker($query, bool $expected)
    {
        if ($expected === false) {
            return $query->whereNull('packer_user_id');
        }

        return $query->whereNotNull('packer_user_id');
    }

    /**
     * @param mixed $query
     * @param bool $expected
     *
     * @return mixed
     */
    public function scopeIsPicked($query, bool $expected)
    {
        if ($expected === true) {
            return $query->whereIsPicked();
        }

        return $query->whereIsNotPicked();
    }

    /**
     * @param mixed $query
     * @param bool $is_packing
     *
     * @return mixed
     */
    public function scopeIsPacking($query, bool $is_packing)
    {
        if ($is_packing) {
            return $query->whereNotNull('packer_user_id');
        }

        return $query->whereNull('packer_user_id');
    }

    /**
     * @param mixed $query
     *
     * @return mixed
     */
    public function scopeWhereIsPicked($query)
    {
        return $query->whereNotNull('picked_at');
    }

    /**
     * @param mixed $query
     *
     * @return mixed
     */
    public function scopeWhereIsNotPicked($query)
    {
        return $query->whereNull('picked_at');
    }

    public function scopeIsPacked($query)
    {
        return $query->whereNull('packed_at');
    }

    public function getIsPackedAttribute()
    {
        return $this->packed_at !== null;
    }

    public function setIsPackedAttribute($value)
    {
        $this->packed_at = $value ? now() : null;
    }

    public function getIsPickedAttribute()
    {
        return $this->picked_at !== null;
    }

    public function setIsPickedAttribute($value)
    {
        $this->picked_at = $value ? now() : null;
    }


    /**
     * @return BelongsTo
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_code', 'code');
    }

    /**
     * @return HasMany
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * @return HasMany
     */
    public function packlist(): HasMany
    {
        return $this->hasMany(Packlist::class);
    }

    /**
     * @return BelongsTo
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(OrderAddress::class);
    }

    /**
     * @return BelongsTo
     */
    public function packer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'packer_user_id');
    }

    /**
     * @return HasOne
     */
    public function orderProductsTotals(): HasOne
    {
        return $this->hasOne(OrderProductTotal::class, 'order_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function orderShipments(): HasMany
    {
        return $this->hasMany(ShippingLabel::class)->latest();
    }

    /**
     * @return HasMany | OrderComment
     */
    public function orderComments()
    {
        return $this->hasMany(OrderComment::class)->orderByDesc('id');
    }

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::scope('search', 'whereHasText')->ignore([null, '']),
                AllowedFilter::exact('status', 'status_code'),
                AllowedFilter::exact('id', 'id'),
                AllowedFilter::exact('order_id', 'id'),
                AllowedFilter::exact('order_number')->ignore([null, '']),
                AllowedFilter::partial('shipping_method_code')->ignore([null, '']),
                AllowedFilter::exact('is_active'),
                AllowedFilter::exact('is_on_hold'),
                AllowedFilter::exact('packer_user_id'),

                AllowedFilter::scope('age_in_days', 'whereAgeInDays')->ignore([null, '']),
                AllowedFilter::scope('is_picked'),
                AllowedFilter::scope('is_packed'),
                AllowedFilter::scope('is_packing'),
                AllowedFilter::scope('packed_between'),

                AllowedFilter::scope('has_packer'),

                AllowedFilter::scope('inventory_source_warehouse_id', 'addInventorySource')->ignore([null, '']),

                AllowedFilter::scope('has_tags', 'withAllTags'),
                AllowedFilter::scope('without_tags', 'withoutAllTags'),
            ])
            ->allowedIncludes([
                AllowedInclude::relationship('activities', 'activities'),
                AllowedInclude::relationship('activities.causer', 'activities.causer'),
                AllowedInclude::relationship('shipping_address', 'shippingAddress'),
                AllowedInclude::relationship('order_shipments', 'orderShipments'),
                AllowedInclude::relationship('order_products', 'orderProducts'),
                AllowedInclude::relationship('order_products_totals', 'orderProductsTotals'),
                AllowedInclude::relationship('order_products.product', 'orderProducts.product'),
                AllowedInclude::relationship('order_products.product.aliases', 'orderProducts.product.aliases'),
                AllowedInclude::relationship('packer', 'packer'),
                AllowedInclude::relationship('order_comments', 'orderComments'),
                AllowedInclude::relationship('order_comments.user', 'orderComments.user'),
                AllowedInclude::relationship('tags', 'tags'),
            ])
            ->allowedSorts([
                'updated_at',
                'order_placed_at',
                'packed_at',
                'order_closed_at',
                'min_shelf_location',
            ]);
    }
}
