<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Widget::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'config' => $faker->word,
    ];
});
