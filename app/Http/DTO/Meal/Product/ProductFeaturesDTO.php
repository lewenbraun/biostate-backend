<?php

namespace App\Http\DTO\Meal\Product;

use Illuminate\Http\Request;

class ProductFeaturesDTO
{
    public $weight_for_features;
    public $calories;
    public $proteins;
    public $carbs;
    public $fats;

    public static function fromRequest(Request $request)
    {
        $dto = new self();
        $dto->weight_for_features = $request->weight_for_features;
        $dto->calories = $request->calories;
        $dto->proteins = $request->proteins;
        $dto->carbs = $request->carbs;
        $dto->fats = $request->fats;
        return $dto;
    }
}
