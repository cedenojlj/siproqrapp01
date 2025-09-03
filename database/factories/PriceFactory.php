<?php

namespace Database\Factories;

use App\Models\Price;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'customer_id' => Customer::factory(),
            'price_quantity' => $this->faker->randomFloat(2, 10, 1000),
            'price_weight' => $this->faker->randomFloat(2, 20, 2000),
        ];
    }
}
