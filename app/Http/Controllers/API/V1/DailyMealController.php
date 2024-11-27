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
    public function createMeal(Request $request)
    {
        $dailyMeal = DailyMeal::create([
           'date' => $request->date,
           'meal_order' => $request->meal_order,
           'user_id' => auth()->id(),
        ]);

        return $dailyMeal;
    }

    public function deleteMeal(Request $request)
    {
        $dailyMeal = DailyMeal::findOrFail($request->meal_id)->delete();

        return $dailyMeal;
    }

    public function addProduct(AddProductToMealRequest $request)
    {
        // Один запрос для нахождения либо существующего DailyMeal, либо возвращения null
        $dailyMeal = DailyMeal::with('products')
            ->where('date', $request->date)
            ->where('meal_order', $request->meal_order)
            ->first();

        // Если запись не найдена, создаем новую
        if (is_null($dailyMeal)) {
            $dailyMeal = DailyMeal::create([
                'date' => $request->date,
                'meal_order' => $request->meal_order,
                'user_id' => auth()->id(),
            ]);

            // Создаем запись для продукта, если не было DailyMeal
            DailyMealProduct::create([
                'product_id' => $request->product_id,
                'daily_meal_id' => $dailyMeal->id,
            ]);

            return new DailyMealResource($dailyMeal);
        }

        // Ищем продукт среди связанных продуктов
        $product = $dailyMeal->products->firstWhere('id', $request->product_id);

        if ($product) {
            // Если продукт уже есть, увеличиваем значение count
            $product->pivot->count += 1;
            $product->pivot->save();
        } else {
            // Если продукта нет, создаем новую запись
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

    public function deleteProduct(Request $request)
    {
        try {
            $dailyMeal = DailyMeal::findOrFail($request->meal_id);
            $dailyMeal->products()->detach($request->product_id);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function increaseCountProduct(Request $request)
    {
        try {
            $dailyMeal = DailyMeal::findOrFail($request->meal_id);
            $product = $dailyMeal->products->findOrFail($request->product_id);
            $product->pivot->count += 1;
            $product->pivot->save();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function decreaseCountProduct(Request $request)
    {
        try {
            $dailyMeal = DailyMeal::findOrFail($request->meal_id);
            $product = $dailyMeal->products->findOrFail($request->product_id);

            if ($product->pivot->count === 1) {
                $dailyMeal->products()->detach($request->product_id);
            } else {
                $product->pivot->count -= 1;
                $product->pivot->save();
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
