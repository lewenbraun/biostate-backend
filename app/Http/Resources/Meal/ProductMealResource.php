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

        $productFeaturesDTO = ProductFeaturesDTO::fromResource($this);

        $formattedProductFeatures = (new ProductService())->formatFeatures($productFeaturesDTO);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'weight' => $weight,
            'image' => $this->image,
            'calories' => $formattedProductFeatures->calories,
            'proteins' => $formattedProductFeatures->proteins,
            'carbs' => $formattedProductFeatures->carbs,
            'fats' => $formattedProductFeatures->fats,
            'count' => $this->pivot->count,
        ];
    }

}
