<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pick;
use App\Models\Product;
use App\User;

class PickFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::query()->inRandomOrder()->first() ?? Product::factory();

        $user = User::query()->inRandomOrder()->first() ?? User::factory()->create();

        $skippingPick = (rand(1, 20) === 1);

        return [
            'product_id'               => $product->getKey(),
            'sku_ordered'              => $product->sku,
            'name_ordered'             => $product->name,
            'user_id'                  => $user->getKey(),
            'quantity_picked'          => $skippingPick ? 0 : $this->faker->numberBetween(1, 50),
            'quantity_skipped_picking' => $skippingPick ? $this->faker->numberBetween(1, 50) : 0,
        ];
    }
}
