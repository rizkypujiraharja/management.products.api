<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $productNameBricks = [
            'adjective' => ['Small', 'Ergonomic', 'Rustic', 'Intelligent', 'Gorgeous', 'Incredible', 'Fantastic', 'Practical', 'Sleek', 'Awesome', 'Enormous', 'Mediocre', 'Synergistic', 'Heavy Duty', 'Lightweight', 'Aerodynamic', 'Durable'],
            'material'  => ['Steel', 'Wooden', 'Concrete', 'Plastic', 'Cotton', 'Granite', 'Rubber', 'Leather', 'Silk', 'Wool', 'Linen', 'Marble', 'Iron', 'Bronze', 'Copper', 'Aluminum', 'Paper'],
            'product'   => ['Chair', 'Car', 'Computer', 'Gloves', 'Pants', 'Shirt', 'Table', 'Shoes', 'Hat', 'Plate', 'Knife', 'Bottle', 'Coat', 'Lamp', 'Keyboard', 'Bag', 'Bench', 'Clock', 'Watch', 'Wallet'],
        ];

        $randomProductName = $this->faker->randomElement($productNameBricks['adjective'])
        .' '.$this->faker->randomElement($productNameBricks['material'])
        .' '.$this->faker->randomElement($productNameBricks['product']);

        return [
            'sku'                   => (string) $this->faker->unique()->randomNumber(6),
            'name'                  => $randomProductName,
            'price'                 => $this->faker->randomFloat(2, 0, 1000),
            'sale_price'            => $this->faker->randomFloat(2, 0, 1000),
            'sale_price_start_date' => $this->faker->dateTimeBetween('-1 year', '+5 months'),
            'sale_price_end_date'   => $this->faker->dateTimeBetween('-1 month', '+1 year'),
            'commodity_code'        => $this->faker->randomElement(['6109100010', '6110309100', '6115210000']),
        ];
    }
}
