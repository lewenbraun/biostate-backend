<?php

namespace App\Http\Services\Meal;

use App\Models\Meal;
use App\Models\MealProduct;
use Illuminate\Http\Request;
use App\Http\DTO\Meal\Product\ProductFeaturesDTO;
use App\Http\DTO\Meal\Product\FormattedProductFeaturesDTO;

final class ProductService
{
    private const WEIGHT_FACTOR_BASE = 100;

    public function getFormattedProductData(Request $request): array
    {
        $productData = [
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'weight' => $request->weight,
            'weight_for_features' => $request->weight_for_features,
            'is_public' => $request->is_public,
            'is_alcohol' => $request->is_alcohol,
        ];

        if ($request->weight_for_features) {
            $productFeatures = ProductFeaturesDTO::fromRequest($request);
            $formattedFeatures = $this->formatFeatures($productFeatures);
            $formattedProductData = array_merge($productData, $formattedFeatures->toArray());

            return $formattedProductData;
        }

        return $productData;
    }

    public function addProductOrIncreaseCountIntoMeal(int $product_id, float $weight, Meal $meal): void
    {
        $product = $meal->products->where('id', $product_id)->where('pivot.weight_product', $weight)->first();

        if ($product) {
            $this->increaseCountProduct($product);
        } else {
            MealProduct::create([
                'product_id' => $product_id,
                'meal_id' => $meal->id,
                'weight_product' => $weight,
            ]);
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

    public function formatFeatures(ProductFeaturesDTO $productFeatures): FormattedProductFeaturesDTO
    {
        $factor = ($productFeatures->weight ?? self::WEIGHT_FACTOR_BASE) / $productFeatures->weight_for_features;
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
