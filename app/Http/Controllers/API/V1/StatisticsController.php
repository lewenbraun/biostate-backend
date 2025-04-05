<?php

declare(strict_types=1);

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
            $meals = Meal::where('date', $request->date('date'))
                ->where('user_id', auth()->id())
                ->get();

            return response()->json($meals);
        } catch (\Exception $e) {
            Log::error(__('log_error.fetching_daily_statistics') . $e->getMessage());
            return response()->json(['message' => __('errors.fetching_daily_statistics')], 500);
        }
    }

    public function sumNutrientsForPeriodDate(NutruentsForPeriodDateRequest $request): JsonResponse
    {
        try {
            $startDate = $request->date('start_date');
            $endDate = $request->date('end_date');
            $nutrients = $request->input('nutrients');
            $dataDays = $this->nutrientsFormatService->getNutrientDataForPeriod($startDate, $endDate, $nutrients);

            return response()->json($dataDays);
        } catch (\Exception $e) {
            Log::error(__('log_error.calculating_nutrients_for_period') . $e->getMessage());
            return response()->json(['message' => __('errors.calculating_nutrients_for_the_period')], 500);
        }
    }
}
