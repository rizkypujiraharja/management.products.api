<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesAutostatusPickingConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_autostatus_picking_configurations', function (Blueprint $table) {
            $table->id();
            $table->integer('max_batch_size')->nullable(false)->default(10);
            $table->integer('max_order_age')->nullable(false)->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules_autostatus_picking_configurations');
    }
}
