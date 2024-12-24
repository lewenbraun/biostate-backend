<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'weight_default' => $this->weight_default,
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
