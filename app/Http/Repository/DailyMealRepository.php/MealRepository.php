<?php

namespace App\Http\Repository\DailyMealRepository;

use App\Models\Meal;
use App\Models\User;

class MealRepository
{
    public function getParametersPerDay($date)
    {
        $meals = Meal::where('date', $date)
            ->where('user_id', auth()->id())
            ->get();
    }

    public function getMaxParametersPerDay($date)
    {
        $parameters = User::select('calories', 'proteins', 'fats', 'carbs')
            ->where('id', auth()->id())
            ->first();

        return $parameters;
    }
}
