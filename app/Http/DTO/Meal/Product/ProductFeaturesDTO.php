<?php

declare(strict_types=1);

namespace App\Http\DTO\Meal\Product;

use Illuminate\Http\Request;

class ProductFeaturesDTO
{
    public float $weight_for_features;
    public ?float $weight;
    public ?float $calories;
    public ?float $proteins;
    public ?float $carbs;
    public ?float $fats;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->weight_for_features = (int) $request->input('weight_for_features', 0);
        $dto->weight =  $request->has('weight') ? $request->weight : null;
        $dto->calories = $request->has('calories') ? $request->calories : null;
        $dto->proteins = $request->has('proteins') ? (float) $request->proteins : null;
        $dto->carbs = $request->has('carbs') ? (float) $request->carbs : null;
        $dto->fats = $request->has('fats') ? (float) $request->fats : null;
        return $dto;
    }
}
