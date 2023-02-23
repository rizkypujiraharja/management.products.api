<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use App\Modules\InventoryReservations\src\Models\Configuration;
use App\Traits\LogsActivityTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class WarehouseInventoryDashboardReport extends Report
{
    use LogsActivityTrait;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->view = 'reports.inventory-dashboard';

        $this->report_name = 'Inventory Dashboard';

        if (request('title')) {
            $this->report_name = request('title').' ('.$this->report_name.')';
        }

        $this->fields = [
            'warehouse_id'               => 'inventory.warehouse_id',
            'warehouse_code'             => 'inventory.warehouse_code',
            'never_counted'              => DB::raw('count(CASE WHEN inventory.last_counted_at IS NULL THEN 1 END)'),
            'missing_restock_levels'     => DB::raw('count(CASE WHEN inventory.restock_level <= 0 THEN 1 END)'),
            'products_on_minus'          => DB::raw('count(CASE WHEN inventory.quantity_available < 0 THEN 1 END)'),
            'wh_products_available'      => DB::raw('count(*)'),
            'wh_products_out_of_stock'   => DB::raw('count(CASE WHEN inventory.quantity_available = 0 AND inventory.restock_level > 0 THEN 1 END)'),
            'wh_products_required'       => DB::raw('count(CASE WHEN inventory.quantity_required > 0 THEN 1 END)'),
            'wh_products_incoming'       => DB::raw('count(CASE WHEN inventory.quantity_incoming > 0 THEN 1 END)'),
            'wh_products_stock_level_ok' => DB::raw('count(CASE ' .
                'WHEN (inventory.quantity_required = 0 AND inventory.restock_level > 0) ' .
                'THEN 1 ' .
                'END)'),
        ];

        $inventoryReservationWarehouseIdId = Configuration::first()->warehouse_id;
        $this->baseQuery = Inventory::query()
            ->leftJoin('inventory as inventory_source', function ($join) {
                $join->on('inventory_source.product_id', '=', 'inventory.product_id');
            })
            ->leftJoin('products as product', 'inventory.product_id', '=', 'product.id')
            ->where('inventory_source.warehouse_code', '=', '99')
            ->where('inventory_source.quantity_available', '>', 0)
            ->whereNotIn('inventory.warehouse_code', ['99', '100'])
            ->where('inventory.warehouse_id', '!=', $inventoryReservationWarehouseIdId)
            ->groupBy('inventory.warehouse_code', 'inventory.warehouse_id');

        $this->setPerPage(100);

        $this->casts = [
            'warehouse_id'                => 'integer',
            'warehouse_code'              => 'string',
            'products_on_minus'           => 'float',
            'wh_products_available'       => 'float',
            'wh_products_out_of_stock'    => 'float',
            'wh_products_required'        => 'float',
            'wh_products_incoming'        => 'float',
            'wh_products_stock_level_ok'  => 'float',
        ];

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->withAllTags($value);
                });
            })
        );
    }

    public function saveSnapshotToActivity()
    {
        $this->log('snapshot', $this->queryBuilder()->get()->toArray());

        return $this;
    }
}
