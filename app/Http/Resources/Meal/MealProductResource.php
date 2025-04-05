<?php

declare(strict_types=1);

namespace App\Http\Resources\Meal;

use App\Models\MealProduct;
use Illuminate\Http\Request;
use App\Http\Services\Meal\ProductService;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\DTO\Meal\Product\ProductFeaturesDTO;

/**
 * @mixin MealProduct
 */
class MealProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $weight = is_null($this->resource->getRelationValue('pivot')->weight_product) ?
            $this->resource->weight :
            $this->resource->getRelationValue('pivot')->weight_product;

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'price' => $this->resource->price,
            'weight' => $weight,
            'weight_for_features' => $this->resource->weight_for_features,
            'calories' => $this->resource->calories,
            'proteins' => $this->resource->proteins,
            'carbs' => $this->resource->carbs,
            'fats' => $this->resource->fats,
            'count' => $this->resource->pivot->count,
        ];
    }
}
