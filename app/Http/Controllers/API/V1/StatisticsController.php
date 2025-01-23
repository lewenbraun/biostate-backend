<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\Statistics\NutrientsFormatService;
use App\Http\Requests\General\Authorize\RequiredDateRequest;
use App\Http\Requests\Statistics\NutruentsForPeriodDateRequest;

class StatisticsController extends Controller
{
    private $nutrientsFormatService;

    public function __construct(NutrientsFormatService $nutrientsFormatService)
    {
        $this->nutrientsFormatService = $nutrientsFormatService;
    }

    public function statisticsPerDay(RequiredDateRequest $request): JsonResponse
    {
        $meals = Meal::where('date', $request->date)
            ->where('user_id', auth()->id())
            ->get();

        return $meals;
    }

    public function sumNutrientsForPeriodDate(NutruentsForPeriodDateRequest $request): JsonResponse
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $nutrients = $request->nutrients;
        $dataDays = $this->nutrientsFormatService->getNutrientDataForPeriod($startDate, $endDate, $nutrients);

        return response()->json($dataDays);
    }
}
