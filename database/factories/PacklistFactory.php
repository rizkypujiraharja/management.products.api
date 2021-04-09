<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Packlist::class, function (Faker $faker) {
    return [
        'product_id' => factory(App\Models\Product::class),
        'order_id' => factory(App\Models\Order::class),
        'picker_user_id' => factory(App\User::class),
    ];
});
