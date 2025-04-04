<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
            'user_id' => 1,
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'weight' => $this->faker->numberBetween(10, 200),
            'weight_for_features' => 100,
            'calories' => $this->faker->numberBetween(50, 500),
            'proteins' => $this->faker->randomFloat(1, 0, 50),
            'carbs' => $this->faker->randomFloat(1, 0, 100),
            'fats' => $this->faker->randomFloat(1, 0, 50),
        ];
    }
}
