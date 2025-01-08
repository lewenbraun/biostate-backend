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

    public function sumNutrientsForPeriodDate(Request $request): JsonResponse
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $nutrient = $request->nutrient;
        $dataDays = $this->statisticsRepository->getNutrientDataForPeriod($startDate, $endDate, $nutrient);
        return response()->json($dataDays);
    }
}
