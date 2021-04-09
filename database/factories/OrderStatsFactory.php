<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\OrderStats::class, function (Faker $faker) {
    return [
        'order_id' => $faker->randomNumber(),
        'age_in_days' => $faker->randomNumber(),
    ];
});
