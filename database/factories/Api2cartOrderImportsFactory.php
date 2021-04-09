<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Modules\Api2cart\src\Models\Api2cartOrderImports::class, function (Faker $faker) {
    return [
        'connection_id' => $faker->randomNumber(),
        'order_id' => $faker->randomNumber(),
        'when_processed' => $faker->dateTime(),
        'order_number' => $faker->word,
        'raw_import' => $faker->word,
    ];
});
