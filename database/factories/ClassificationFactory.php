<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classification>
 */
class ClassificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->swiftBicNumber(),
            'description' => $this->faker->sentence(),
            'size' => $this->faker->randomElement(['small', 'medium', 'large']),
            'precio_unidad' => $this->faker->randomFloat(2, 10, 1000),
            'precio_peso' => $this->faker->randomFloat(2, 20, 2000),
            'unit_type' => $this->faker->randomElement(['Peso', 'Cantidad']),
            'name' => $this->faker->name(),
        ];
    }
}
