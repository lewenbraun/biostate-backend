<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MealProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MealProduct>
 */
class MealProductFactory extends Factory
{
    protected $model = MealProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meal_id' => $this->faker->randomNumber(),
            'product_id' => $this->faker->randomNumber(),
            'count' => $this->faker->numberBetween(1, 5),
            'weight_product' => $this->faker->randomFloat(2, 0.1, 50),
        ];
    }
}
