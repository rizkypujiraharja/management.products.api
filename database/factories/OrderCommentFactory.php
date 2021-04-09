<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\OrderComment::class, function (Faker $faker) {
    return [
        'order_id' => $faker->randomNumber(),
        'user_id' => factory(App\User::class),
        'comment' => $faker->word,
    ];
});
