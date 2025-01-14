<?php

namespace Database\Factories\Spatie\Tags;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Tags\Tag;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
