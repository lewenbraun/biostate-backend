<?php

namespace App\Http\Services\Statistics;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Meal;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class NutrientsFormatService
{
    /**
     * @param array<int, string> $nutrients
     * @return array<string, array<int, array{date: string, total: float}>>
     */
    public function getNutrientDataForPeriod(string $startDate, string $endDate, array $nutrients): array
    {
        /** @var User $user */
        $user = auth()->user();
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $mealsForPeriod = $this->getMealsForPeriod($user, $startDate, $endDate, $nutrients);
        $data = [];

        foreach ($nutrients as $nutrient) {
            $iterableDate = $startDate->copy();
            $daysData = [];

            while ($iterableDate->lte($endDate)) {
                /** @var Collection<int, Meal> $dailyMeals */
                $dailyMeals = $mealsForPeriod->filter(
                    fn (Meal $meal) => $meal->date->isSameDay($iterableDate)
                );

                if ($dailyMeals->isEmpty()) {
                    $daysData[] = [
                        'date' => $iterableDate->format('d.m'),
                        'total' => 0,
                    ];
                    $iterableDate->addDay();
                    continue;
                }

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

    /**
     * @param Collection<int, Meal> $meals
     */
    private function calculateDailyNutrientTotal(string $nutrient, Collection $meals): float
    {
        $sumMeals = $meals->sum(
            fn (Meal $meal) => $meal->products->sum(function (Product $product) use ($nutrient): float {
                if (!property_exists($product, 'pivot') || $product->pivot === null) {
                    return 0.0;
                }
                $portionWeight = ($product->getRelationValue('weight_product') / $product->getRelationValue('weight_for_features')) * $product->getRelationValue('pivot')->count;
                return $product->$nutrient * $portionWeight;
            })
        );

        return round($sumMeals, 2);
    }

    /**
     * @param array<int, string> $nutrients
     * @return Collection<int, Meal>
     */
    private function getMealsForPeriod(User $user, Carbon $startDate, Carbon $endDate, array $nutrients): Collection
    {
        $selectWithNutrients = array_merge(
            ['products.id', 'weight_for_features', 'meal_product.weight_product'],
            $nutrients
        );

        return $user->meals()
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['products' => function ($query) use ($selectWithNutrients): void {
                $query->select($selectWithNutrients);
            }])
            ->get();
    }
}
