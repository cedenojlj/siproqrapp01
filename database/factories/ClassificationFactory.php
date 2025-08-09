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
            'unit_type' => $this->faker->randomElement(['Peso', 'Cantidad']),
        ];
    }
}
