<?php

namespace App\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Repository\StatisticsRepository;

class StatisticsController extends Controller
{
    private $statisticsRepository;

    public function __construct(StatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function getCaloriesPerWeek(Request $request)
    {
        $startDay = Carbon::now()->startOfWeek();
        $endDay = Carbon::now()->endOfWeek();
        $caloriesPerWeek = $this->statisticsRepository->getCaloriesForDate($startDay, $endDay);
    }
}
