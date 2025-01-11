<?php

namespace App\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\Statistics\NutrientsFormatService;

class StatisticsController extends Controller
{
    private $nutrientsFormatService;

    public function __construct(NutrientsFormatService $nutrientsFormatService)
    {
        $this->nutrientsFormatService = $nutrientsFormatService;
    }

    public function sumNutrientsForPeriodDate(Request $request): JsonResponse
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $nutrients = $request->nutrients;
        $dataDays = $this->nutrientsFormatService->getNutrientDataForPeriod($startDate, $endDate, $nutrients);

        return response()->json($dataDays);
    }
}
