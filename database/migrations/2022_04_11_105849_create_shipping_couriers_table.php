<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingCouriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_couriers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 25)->unique()->nullable(false);
            $table->string('service_provider_class');
            $table->timestamps();
        });

        \App\Models\ShippingCourier::query()->create([
            'code' => 'dpd_label',
            'service_provider_class' => '',
        ]);

        \App\Models\ShippingCourier::query()->create([
            'code' => 'dpd_uk',
            'service_provider_class' => '',
        ]);

        \App\Models\ShippingCourier::query()->create([
            'code' => 'an_post',
            'service_provider_class' => '',
        ]);

        \App\Models\ShippingCourier::query()->create([
            'code' => 'address_label',
            'service_provider_class' => '',
        ]);
    }
}
