<?php

declare(strict_types=1);

namespace App\Http\Services\Meal\Contracts;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Model;

interface ProductServiceInterface
{
    /**
     * Adds a product to a meal or increases its count if it already exists.
     *
     * @param int $product_id The ID of the product to add.
     * @param float $weight The weight of the product in the meal.
     * @param Meal $meal The Meal model instance to add the product to.
     * @return void
     */
    public function addProductOrIncreaseCountIntoMeal(int $product_id, float $weight, Meal $meal): void;

    /**
     * Increases the count of a product in a meal.
     *
     * @param Model $product The product model instance with the pivot attribute.
     * @return void
     */
    public function increaseCountProduct($product): void;

    /**
     * Decreases the count of a product in a meal.
     *
     * @param Model $product The product model instance with the pivot attribute.
     * @return void
     */
    public function decreaseCountProduct($product): void;
}
