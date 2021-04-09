<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Warehouse::class, function (Faker $faker) {
    return [
        'code' => $faker->word,
        'name' => $faker->name,
        'deleted_at' => $faker->dateTime(),
    ];
});
