<?php

namespace App\Http\Services\Meal;

use App\Http\DTO\Meal\Product\FormattedProductFeaturesDTO;
use App\Http\DTO\Meal\Product\ProductFeaturesDTO;
use App\Models\Meal;
use App\Models\MealProduct;

final class ProductService
{
    private const WEIGHT_FACTOR_BASE = 100;

    public function addProductOrIncreaseCountIntoMeal(int $product_id, float $weight, Meal $meal)
    {
        try {
            $product = $meal->products->firstWhere('id', $product_id);

            if ($product) {
                $this->increaseCountProduct($product);
            } else {
                MealProduct::create([
                    'product_id' => $product_id,
                    'meal_id' => $meal->id,
                    'weight_product' => $weight,
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

    public function formatWeight(ProductFeaturesDTO $productFeatures): FormattedProductFeaturesDTO
    {
        $factor = self::WEIGHT_FACTOR_BASE / $productFeatures->weight_for_features;

        $formattedFeatures = new FormattedProductFeaturesDTO();

        $formattedFeatures->calories = self::calculateQuantity($productFeatures->calories, $factor);
        $formattedFeatures->proteins = self::calculateQuantity($productFeatures->proteins, $factor);
        $formattedFeatures->carbs = self::calculateQuantity($productFeatures->carbs, $factor);
        $formattedFeatures->fats = self::calculateQuantity($productFeatures->fats, $factor);

        return $formattedFeatures;
    }

    private function calculateQuantity(?float $feature, float $factor): float
    {
        return round($feature * $factor);
    }

}
