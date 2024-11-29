<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Meal;
use App\Models\MealProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Meal\MealResource;
use App\Http\Requests\Meal\AddProductToMealRequest;

class MealController extends Controller
{
    public function createMeal(Request $request)
    {
        $meal = Meal::create([
           'date' => $request->date,
           'meal_order' => $request->meal_order,
           'user_id' => auth()->id(),
        ]);

        return $meal;
    }

    public function deleteMeal(Request $request)
    {
        $meal = Meal::findOrFail($request->meal_id)->delete();

        return $meal;
    }

    public function addProduct(AddProductToMealRequest $request)
    {
        // Один запрос для нахождения либо существующего Meal, либо возвращения null
        $meal = Meal::with('products')
            ->where('date', $request->date)
            ->where('meal_order', $request->meal_order)
            ->first();

        // Если запись не найдена, создаем новую
        if (is_null($meal)) {
            $meal = Meal::create([
                'date' => $request->date,
                'meal_order' => $request->meal_order,
                'user_id' => auth()->id(),
            ]);

            // Создаем запись для продукта, если не было Meal
            MealProduct::create([
                'product_id' => $request->product_id,
                'meal_id' => $meal->id,
            ]);

            return new MealResource($meal);
        }

        // Ищем продукт среди связанных продуктов
        $product = $meal->products->firstWhere('id', $request->product_id);

        if ($product) {
            // Если продукт уже есть, увеличиваем значение count
            $product->pivot->count += 1;
            $product->pivot->save();
        } else {
            // Если продукта нет, создаем новую запись
            MealProduct::create([
                'product_id' => $request->product_id,
                'meal_id' => $meal->id,
            ]);
        }
    }

    public function show(Request $request)
    {
        $meal = Meal::where('date', $request->date)
            ->where('user_id', auth()->id())
            ->get();

        return MealResource::collection($meal);
    }

    public function deleteProduct(Request $request)
    {
        try {
            $meal = Meal::findOrFail($request->meal_id);
            $meal->products()->detach($request->product_id);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function increaseCountProduct(Request $request)
    {
        try {
            $meal = Meal::findOrFail($request->meal_id);
            $product = $meal->products->findOrFail($request->product_id);
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
            $meal = Meal::findOrFail($request->meal_id);
            $product = $meal->products->findOrFail($request->product_id);

            if ($product->pivot->count === 1) {
                $meal->products()->detach($request->product_id);
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
