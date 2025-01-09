<?php

namespace App\Http\Repository;

use Carbon\Carbon;
use App\Models\User;

class StatisticsRepository
{
    public function getNutrientDataForPeriod(string $startDate, string $endDate, string $nutrient): array
    {
        $user = auth()->user();
        $daysData = [];

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        while ($startDate->lte($endDate)) {
            $dailyTotal = $this->calculateDailyNutrientTotal($user, $startDate, $nutrient);

            $daysData[] = [
                'date' => $startDate->toDateString(),
                'total' => $dailyTotal,
            ];

            $startDate->addDay();
        }

        return $daysData;
    }

    private function calculateDailyNutrientTotal(User $user, Carbon $date, string $nutrient): float
    {
        $meals = $user->meals()
            ->whereDate('date', $date)
                ->with(['products' => function ($query) use ($nutrient) {
                    $query->select('products.id', $nutrient, 'weight_for_features', 'meal_product.weight_product');
                }])
            ->get();

        $sumMeals = $meals->sum(function ($meal) use ($nutrient) {
            return $meal->products->sum(function ($product) use ($nutrient) {
                $portionWeight = $product->pivot->weight_product / $product->weight_for_features;
                return $product->$nutrient * $portionWeight;
            });
        });

        return $sumMeals;
    }
}
