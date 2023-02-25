<?php

use App\Modules\InventoryReservations\src\EventServiceProviderBase;
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
        Schema::create('modules_inventory_reservations_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id');
            $table->timestamps();

            $table->foreign('warehouse_id', 'modules_inventory_reservations_warehouse_id_foreign')
                ->references('id')
                ->on('warehouses');

            $table->index('warehouse_id', 'modules_inventory_reservations_warehouse_id_index');
        });

        EventServiceProviderBase::enableModule();
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
