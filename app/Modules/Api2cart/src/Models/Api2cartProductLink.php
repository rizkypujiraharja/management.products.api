<?php

namespace App\Modules\Api2cart\src\Models;

use App\BaseModel;
use App\Models\Inventory;
use App\Models\Product;
use App\Modules\Api2cart\src\Services\Api2cartService;
use App\Modules\Api2cart\src\Transformers\ProductTransformer;
use Barryvdh\LaravelIdeHelper\Eloquent;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class Api2cartProductLink.
 *
 * @property int $id
 * @property int $product_id
 * @property Product $product
 * @property string|null $api2cart_product_type
 * @property string|null $api2cart_product_id
 * @property Carbon $last_fetched_at
 * @property array $last_fetched_data
 * @property Api2cartConnection $api2cartConnection
 * @property string|null $api2cart_connection_id
 * @property float|null $api2cart_quantity
 * @property float|null $api2cart_price
 * @property float|null $api2cart_sale_price
 * @property Carbon|null $api2cart_sale_price_start_date
 * @property Carbon|null $api2cart_sale_price_end_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property array raw_import
 *
 * @method static Builder|Api2cartProductLink newModelQuery()
 * @method static Builder|Api2cartProductLink newQuery()
 * @method static Builder|Api2cartProductLink query()
 * @method static Builder|Api2cartProductLink whereApi2cartConnectionId($value)
 * @method static Builder|Api2cartProductLink whereApi2cartPrice($value)
 * @method static Builder|Api2cartProductLink whereApi2cartProductId($value)
 * @method static Builder|Api2cartProductLink whereApi2cartProductType($value)
 * @method static Builder|Api2cartProductLink whereApi2cartQuantity($value)
 * @method static Builder|Api2cartProductLink whereApi2cartSalePrice($value)
 * @method static Builder|Api2cartProductLink whereApi2cartSalePriceEndDate($value)
 * @method static Builder|Api2cartProductLink whereApi2cartSalePriceStartDate($value)
 * @method static Builder|Api2cartProductLink whereCreatedAt($value)
 * @method static Builder|Api2cartProductLink whereId($value)
 * @method static Builder|Api2cartProductLink whereLastFetchedAt($value)
 * @method static Builder|Api2cartProductLink whereLastFetchedData($value)
 * @method static Builder|Api2cartProductLink whereProductId($value)
 * @method static Builder|Api2cartProductLink whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Api2cartProductLink extends BaseModel
{
    protected $table = 'modules_api2cart_product_links';

    /**
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'last_fetched_at',
        'api2cart_connection_id',
        'api2cart_product_type',
        'api2cart_product_id',
        'api2cart_quantity',
        'api2cart_price',
        'api2cart_sale_price',
        'api2cart_sale_price_start_date',
        'api2cart_sale_price_end_date',
        'last_fetched_data',
    ];

    protected $dates = [
        'sale_price_start_date',
        'sale_price_end_date',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'last_fetched_data' => 'array',
    ];

    /**
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = []): bool
    {
        if ($this->last_fetched_data) {
            $product_data = $this->last_fetched_data;

            $sprice_create = data_get($product_data, 'sprice_create', '2000-01-01 00:00:00');
            $sprice_expire = data_get($product_data, 'sprice_expire', '2000-01-01 00:00:00');

            $this->last_fetched_at                = now();
            $this->api2cart_product_id            = data_get($product_data, 'id');
            $this->api2cart_product_type          = data_get($product_data, 'type');
            $this->api2cart_quantity              = data_get($product_data, 'quantity');
            $this->api2cart_price                 = data_get($product_data, 'price');
            $this->api2cart_sale_price            = data_get($product_data, 'special_price');
            $this->api2cart_sale_price_start_date = Carbon::createFromTimeString($sprice_create)->format('Y-m-d H:i:s');
            $this->api2cart_sale_price_end_date   = Carbon::createFromTimeString($sprice_expire)->format('Y-m-d H:i:s');
        }

        return parent::save($options);
    }

    /**
     * @throws GuzzleException
     */
    public function isInSync(): bool
    {
        $product_data = ProductTransformer::toApi2cartPayload($this);

        $store_id = Arr::has($product_data, 'store_id') ? $product_data['store_id'] : null;

        if ($this->api2cart_product_type === null) {
            $this->updateTypeAndId()->save();
        }

        switch ($this->api2cart_product_type) {
            case 'simple':
            case 'product':
                $product_now = Api2cartService::getSimpleProductInfo($this->api2cartConnection, $this->product->sku);
                break;
            case 'variant':
                $product_now = Api2cartService::getVariantInfo($this->api2cartConnection, $this->product->sku);
                break;
            default:
                Log::warning('Update Check FAILED - Could not find product', ['sku' => $product_data['sku']]);
                return false;
        }

        // if product data is null, product does not exist on eCommerce
        // we will delete link
        if (is_null($product_now)) {
            $this->forceDelete();
            return false;
        }

        $this->last_fetched_data = $product_now;
        $this->save();

        $differences = $this->getDifferences($product_data, $product_now);

        if (empty($differences)) {
            return true;
        }

        Log::warning('Update Check FAILED', [
            'type' => $product_now['type'],
            'sku' => $product_now['sku'],
            'store_id' => $store_id,
            'differences' => $differences,
            'now' => $product_now
        ]);

        return false;
    }

    /**
     * @param array $expected
     * @param array $actual
     *
     * @return array
     */
    private function getDifferences(array $expected, array $actual): array
    {
        // initialize variables
        $differences = [];

        $keys_to_verify = [
            'price',
        ];

        if ((Arr::has($actual, 'manage_stock')) && ($actual['manage_stock'] != 'False')) {
            $keys_to_verify = array_merge($keys_to_verify, ['quantity']);
        }

        if ((Arr::has($expected, 'sprice_expire')) &&
            (\Carbon\Carbon::createFromTimeString($expected['sprice_expire'])->isFuture())) {
            $keys_to_verify = array_merge($keys_to_verify, [
                'special_price',
                'sprice_create',
                'sprice_expire',
            ]);
        }

        $expected_data = Arr::only($expected, $keys_to_verify);
        $actual_data = Arr::only($actual, $keys_to_verify);

        foreach (array_keys($expected_data) as $key) {
            if ((!Arr::has($actual_data, $key)) or ($expected_data[$key] != $actual_data[$key])) {
                $differences[$key] = [
                    'expected' => $expected_data[$key],
                    'actual' => $actual_data[$key],
                ];
            }
        }

        return Arr::dot($differences);
    }

    /**
     * @return BelongsTo
     */
    public function api2cartConnection(): BelongsTo
    {
        return $this->belongsTo(Api2cartConnection::class, 'api2cart_connection_id');
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     */
    public function updateTypeAndId(): Api2cartProductLink
    {
        $store_key = $this->api2cartConnection->bridge_api_key;

        $response = Api2cartService::getProductTypeAndId($store_key, $this->product->sku);

        $this->api2cart_product_type = $response['type'];
        $this->api2cart_product_id = $response['id'];

        return $this;
    }
}
