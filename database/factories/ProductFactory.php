<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => $this->faker->name(),            
            'sku' => $this->faker->unique()->swiftBicNumber(),
            'classification_id' => \App\Models\Classification::factory(),
            'type' => $this->faker->word(),
            'size' => $this->faker->word(),
            'GN' => $this->faker->word(),
            'GW' => $this->faker->word(),
            'Box' => $this->faker->word(),
            'invoice_number' => $this->faker->unique()->numberBetween(1000, 9999),
            
        ];
    }
}
