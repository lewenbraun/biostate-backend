<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\Statistics\NutrientsFormatService;
use App\Http\Requests\General\Authorize\RequiredDateRequest;
use App\Http\Requests\Statistics\NutruentsForPeriodDateRequest;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{
    private NutrientsFormatService $nutrientsFormatService;

    public function __construct(NutrientsFormatService $nutrientsFormatService)
    {
        $this->nutrientsFormatService = $nutrientsFormatService;
    }

    public function statisticsPerDay(RequiredDateRequest $request): JsonResponse
    {
        try {
            $meals = Meal::where('date', $request->date)
                ->where('user_id', auth()->id())
                ->get();

            return response()->json($meals);
        } catch (\Exception $e) {
            Log::error('Error fetching daily statistics: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching daily statistics.'], 500);
        }
    }

    public function sumNutrientsForPeriodDate(NutruentsForPeriodDateRequest $request): JsonResponse
    {
        try {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $nutrients = $request->nutrients;
            $dataDays = $this->nutrientsFormatService->getNutrientDataForPeriod($startDate, $endDate, $nutrients);

            return response()->json($dataDays);
        } catch (\Exception $e) {
            Log::error('Error calculating nutrients for period: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while calculating nutrients for the period.'], 500);
        }
    }
}
