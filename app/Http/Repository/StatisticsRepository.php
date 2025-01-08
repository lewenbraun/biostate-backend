<?php

namespace App\Http\Repository;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StatisticsRepository
{
    public function getCaloriesForDate($startDate, $endDate)
    {
        $user = auth()->user();
        $user->meals->with(['products' => function ($query) {
            $query->select('products.id', 'calories', 'meal_product.weight_product');
        }])
            ->sum('calories');
    }

    public function getNutrientDataForPeriod($startDate, $endDate, $nutrient): array
    {
        $user = auth()->user();

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $daysData = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dailyTotal = $user->meals()
                ->where('date', $currentDate->toDateString())
                ->with(['products' => function ($query) use ($nutrient) {
                    $query->select('products.id', $nutrient, 'weight_for_features', 'meal_product.weight_product');
                }])
                ->get()
                ->flatMap(function ($meal) use ($nutrient) {
                    return $meal->products->map(function ($product) use ($nutrient) {
                        return $product->$nutrient * ($product->pivot->weight_product / $product->weight_for_features);
                    });
                })
                ->sum();
            // if (!$dailyTotal->isEmpty()) {
            //     $dailyTotal = 0;
            // } else {
            //     $dailyTotal = $dailyTotal->flatMap(function ($meal) use ($nutrient) {
            //         return $meal->products->map(function ($product) use ($nutrient) {
            //             return $product->$nutrient * ($product->pivot->weight_product / $product->weight_for_features);
            //         });
            //     })->sum();
            //     dd($dailyTotal);
            // }

            $daysData[] = [
                'date' => $currentDate->toDateString(),
                'total' => $dailyTotal,
            ];

            $currentDate->addDay();
        }

        return $daysData;
    }

}
