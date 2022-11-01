<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddModulesMagento2apiProductsIdColumnToModulesMagento2apiProductsPricesComparisonView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE OR REPLACE VIEW modules_magento2api_products_prices_comparison_view AS
            SELECT
                modules_magento2api_products.id as modules_magento2api_products_id,
                products.sku,
                modules_magento2api_connections.magento_store_id,
                modules_magento2api_products.magento_price,
                products_prices.price as expected_price

            FROM `modules_magento2api_products`
            LEFT JOIN products ON products.id = modules_magento2api_products.product_id

            LEFT JOIN modules_magento2api_connections
              ON modules_magento2api_connections.id = modules_magento2api_products.connection_id

            LEFT JOIN products_prices
              ON products_prices.product_id = modules_magento2api_products.product_id
              AND products_prices.warehouse_id = modules_magento2api_connections.pricing_source_warehouse_id
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules_magento2api_products_prices_comparison_view', function (Blueprint $table) {
            //
        });
    }
}