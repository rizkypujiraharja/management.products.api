<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Models\OrderShipment;
use Faker\Generator as Faker;

$factory->define(OrderShipment::class, function (Faker $faker) {
    return [
        'order_id' => Order::inRandomOrder()->first()->getKey(),
        'shipping_number' => $faker->toUpper($faker->randomLetter . $faker->randomLetter . '100' .$faker->randomNumber(8)),
    ];
});
