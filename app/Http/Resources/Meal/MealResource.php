<?php

namespace App\Http\Resources\Meal;

use Illuminate\Http\Request;
use App\Http\Resources\Meal\ProductMealResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
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
            'products' => ProductMealResource::collection($this->products),
            'meal_order' => $this->meal_order,
            'date' => $this->date,
        ];
    }
}
