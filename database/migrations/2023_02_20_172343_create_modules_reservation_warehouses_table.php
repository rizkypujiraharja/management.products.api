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
        if(!Schema::hasTable('modules_reservation_warehouses')) {
            Schema::create('modules_reservation_warehouses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warehouse_id');
                $table->timestamps();

                $table->foreign('warehouse_id')->references('id')->on('warehouses');
                $table->index('warehouse_id');
            });

            $warehouse = \App\Models\Warehouse::firstOrCreate(['code' => '999'], ['name' => '999']);
            \App\Modules\InventoryReservations\src\Models\ReservationWarehouse::create([
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
