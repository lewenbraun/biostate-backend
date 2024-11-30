<?php

namespace App\Http\DTO\Meal\Product;

use Illuminate\Http\Request;

class ProductFeaturesDTO
{
    public $weight;
    public $calories;
    public $proteins;
    public $carbs;
    public $fats;

    public static function fromRequest(Request $request)
    {
        $dto = new self();
        $dto->weight = $request->weight;
        $dto->calories = $request->calories;
        $dto->proteins = $request->proteins;
        $dto->carbs = $request->carbs;
        $dto->fats = $request->fats;
        return $dto;
    }
}
