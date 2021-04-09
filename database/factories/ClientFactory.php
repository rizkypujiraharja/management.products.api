<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Modules\PrintNode\src\Models\Client::class, function (Faker $faker) {
    return [
        'api_key' => $faker->word,
    ];
});
