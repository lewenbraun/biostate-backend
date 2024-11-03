<?php

namespace App\Http\Resources\DailyMeal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyMealResource extends JsonResource
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
            'products' => ProductDailyMealResource::collection($this->products),
            'meal_order' => $this->meal_order,
            'date' => $this->date,
            'weight' => $this->weight,
        ];
    }
}
