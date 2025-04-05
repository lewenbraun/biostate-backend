<?php

declare(strict_types=1);

namespace App\Http\Resources\Meal;

use App\Models\Meal;
use Illuminate\Http\Request;
use App\Http\Resources\Meal\MealProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Meal
 */
class MealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'products' => MealProductResource::collection($this->products),
            'meal_order' => $this->meal_order,
            'date' => $this->date->format('Y-m-d'),
        ];
    }
}
