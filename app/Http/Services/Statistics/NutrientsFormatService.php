<?php

namespace App\Http\Services\Statistics;

use Carbon\Carbon;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NutrientsFormatService
{
    public function getNutrientDataForPeriod(string $startDate, string $endDate, array $nutrients): array
    {
        $user = auth()->user();
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $mealsForPeriod = $this->getMealsForPeriod($user, $startDate, $endDate, $nutrients);
        $data = [];
        foreach ($nutrients as $nutrient) {
            $iterableDate = $startDate->copy();
            $daysData = [];

            while ($iterableDate->lte($endDate)) {
                $dailyMeals = $mealsForPeriod->filter(function ($meal) use ($iterableDate) {
                    return $meal->date->isSameDay($iterableDate);
                });

                $dailyTotal = $this->calculateDailyNutrientTotal($nutrient, $dailyMeals);

                $daysData[] = [
                    'date' => $iterableDate->format('d.m'),
                    'total' => $dailyTotal,
                ];

                $iterableDate->addDay();
            }

            $data[$nutrient] = $daysData;
        }

        return $data;
    }

    private function calculateDailyNutrientTotal(string $nutrient, Collection $meals): float
    {
        $sumMeals = $meals->sum(function ($meal) use ($nutrient) {
            return $meal->products->sum(function ($product) use ($nutrient) {
                $portionWeight = $product->pivot->weight_product / 100 * $product->pivot->count;
                return $product->$nutrient * $portionWeight;
            });
        });

        return round($sumMeals);
    }

    private function getMealsForPeriod(User $user, Carbon $startDate, Carbon $endDate, array $nutrients): Collection
    {
        $selectWithNutrients = array_merge(['products.id', 'weight_for_features', 'meal_product.weight_product'], $nutrients);

        return $user->meals()
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['products' => function ($query) use ($selectWithNutrients) {
                $query->select($selectWithNutrients);
            }])
            ->get();
    }
}
