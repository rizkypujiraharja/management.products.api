<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::query()
            ->chunkById(50, function ($productsList) {
                foreach ($productsList as $product) {
                    $randomPrice = rand(1, 100) - 0.05;

                    ProductPrice::query()
                        ->where('product_id', '=', $product->getKey())
                        ->update([
                            'price' => $randomPrice,
                        ]);
                }
            });
    }
}
