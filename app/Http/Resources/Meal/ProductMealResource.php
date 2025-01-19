<?php

namespace App\Http\Resources\Meal;

use Illuminate\Http\Request;
use App\Http\Services\Meal\ProductService;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\DTO\Meal\Product\ProductFeaturesDTO;

class ProductMealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $weight = is_null($this->pivot->weight_product) ? $this->weight : $this->pivot->weight_product;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'weight' => $weight,
            'weight_for_features' => $this->weight_for_features,
            'image' => $this->image,
            'calories' => $this->calories,
            'proteins' => $this->proteins,
            'carbs' => $this->carbs,
            'fats' => $this->fats,
            'count' => $this->pivot->count,
        ];
    }
}
