<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Meal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Meal\MealResource;
use App\Http\Services\Meal\ProductService;
use App\Http\Requests\Meal\AddProductToMealRequest;

class MealController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

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

    public function addProductIntoMeal(AddProductToMealRequest $request)
    {
        $meal = Meal::with('products')
            ->where('date', $request->date)
            ->where('meal_order', $request->meal_order)
            ->first();

        $this->productService->addProductOrIncreaseCountIntoMeal($request->product_id, $meal);
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
            $this->productService->increaseCountProduct($product);
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
                $this->productService->decreaseCountProduct($product);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
