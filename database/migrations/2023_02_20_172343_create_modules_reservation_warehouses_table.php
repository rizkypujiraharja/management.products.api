<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesReservationWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('modules_inventory_reservations_configurations')) {
            Schema::create('modules_inventory_reservations_configurations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warehouse_id');
                $table->timestamps();

                $table->foreign('warehouse_id', 'modules_inventory_reservations_warehouse_id_foreign')
                    ->references('id')->on('warehouses');
                $table->index('warehouse_id', 'modules_inventory_reservations_warehouse_id_index');
            });

            $warehouse = \App\Models\Warehouse::firstOrCreate(['code' => '999'], ['name' => '999']);
            \App\Modules\InventoryReservations\src\Models\Configuration::create([
                'warehouse_id' => $warehouse->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
