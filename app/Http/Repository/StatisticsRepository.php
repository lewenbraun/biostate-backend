<?php

namespace App\Http\Repository;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StatisticsRepository
{
    public function getCaloriesForDate($startDate, $endDate)
    {
        $user = auth()->user();
        $user->meals->select('calories')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('calories');
    }
}
