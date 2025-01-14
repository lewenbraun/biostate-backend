<?php

namespace App\Http\DTO\Meal\Product;

use Illuminate\Http\Request;

class ProductFeaturesDTO
{
    public $weight_for_features;
    public $weight;
    public $calories;
    public $proteins;
    public $carbs;
    public $fats;

    public static function fromRequest(Request $request)
    {
        $dto = new self();
        $dto->weight_for_features = $request->weight_for_features;
        $dto->weight = $request->weight;
        $dto->calories = $request->calories;
        $dto->proteins = $request->proteins;
        $dto->carbs = $request->carbs;
        $dto->fats = $request->fats;
        return $dto;
    }

    public static function fromResource($resource)
    {
        $dto = new self();
        $dto->weight_for_features = (int) $resource->pivot->weight_product;
        $dto->weight = $resource->weight;
        $dto->calories = $resource->calories;
        $dto->proteins = $resource->proteins;
        $dto->carbs = $resource->carbs;
        $dto->fats = $resource->fats;
        return $dto;
    }
}
