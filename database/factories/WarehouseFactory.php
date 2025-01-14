<?php

namespace Database\Factories;

use App\Models\OrderAddress;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        $address_id = OrderAddress::factory()->create();

        $randomCode = implode('', [
            $this->faker->randomLetter,
            $this->faker->randomLetter,
            $this->faker->randomLetter,
            $this->faker->randomLetter
        ]);

        return [
            'name'  => $this->faker->city,
            'code'  => Str::upper($randomCode),
            'address_id' => $address_id,
        ];
    }
}
