<?php

namespace Database\Factories;

use App\Models\Category;
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
            'name' => $this->faker->word(),
            'description' => $this->faker->optional()->text(200),
            'price' => $this->faker->optional()->randomFloat(2, 1, 100),
            'weight' => $this->faker->optional()->randomFloat(2, 0.1, 5),
            'image' => $this->faker->optional()->imageUrl(640, 480, 'food', true),
            'category_id' => Category::factory(),
            'calories' => $this->faker->optional()->randomFloat(2, 50, 500),
            'proteins' => $this->faker->optional()->randomFloat(2, 0, 50),
            'carbs' => $this->faker->optional()->randomFloat(2, 0, 100),
            'fats' => $this->faker->optional()->randomFloat(2, 0, 50),
        ];
    }
}
