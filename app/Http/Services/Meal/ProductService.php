<?php

namespace App\Http\Services\Meal;

use App\Models\Meal;
use App\Models\MealProduct;

final class ProductService
{
    public function addProductOrIncreaseCountIntoMeal(int $product_id, Meal $meal)
    {
        try {
            $product = $meal->products->firstWhere('id', $product_id);

            if ($product) {
                $this->increaseCountProduct($product);
            } else {
                MealProduct::create([
                    'product_id' => $product_id,
                    'meal_id' => $meal->id,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function increaseCountProduct($product): void
    {
        $product->pivot->count += 1;
        $product->pivot->save();
    }

    public function decreaseCountProduct($product): void
    {
        $product->pivot->count -= 1;
        $product->pivot->save();
    }
}
