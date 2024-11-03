<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\DailyMeal\DailyMealResource;
use App\Models\DailyMeal;
use Illuminate\Http\Request;
use App\Models\DailyMealProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\DailyMeal\AddProductToMealRequest;

class DailyMealController extends Controller
{
    public function addProduct(AddProductToMealRequest $request)
    {
        $dailyMeals = DailyMeal::where('date', $request->date)->get();

        if ($dailyMeals->isEmpty()) {
            $dailyMeal = DailyMeal::create([
                'date' => $request->date,
                'meal_order' => $request->meal_order,
                'user_id' => auth()->id(),
            ]);

            DailyMealProduct::create([
                'product_id' => $request->product_id,
                'daily_meal_id' => $dailyMeal->id,
            ]);
        } else {
            $dailyMeal = $dailyMeals->where('meal_order', $request->meal_order)->first();

            DailyMealProduct::create([
               'product_id' => $request->product_id,
               'daily_meal_id' => $dailyMeal->id,
            ]);
        }
    }

    public function show(Request $request)
    {
        $dailyMeal = DailyMeal::where('date', $request->date)
            ->where('user_id', auth()->id())
            ->get();


        return DailyMealResource::collection($dailyMeal);
    }

    public function delete(Request $request)
    {
        try {
            $dailyMeal = DailyMeal::findOrFail($request->daily_meal_id);

            $dailyMeal->products->where('product_id', $request->product_id)->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
