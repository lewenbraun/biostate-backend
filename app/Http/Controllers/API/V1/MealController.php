<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Meal;
use App\Models\MealProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\Meal\MealResource;
use App\Http\Services\Meal\ProductService;
use App\Http\Requests\Meal\CreateMealRequest;
use App\Http\Requests\Meal\DeleteProductRequest;
use App\Http\Requests\Meal\AddProductToMealRequest;
use App\Http\Requests\Meal\ChangeCountProductRequest;
use App\Http\Requests\Meal\UpdateWeightProductRequest;
use App\Http\Requests\General\Authorize\RequiredIdRequest;
use App\Http\Requests\General\Authorize\RequiredDateRequest;

class MealController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function createMeal(CreateMealRequest $request)
    {
        $meal = Meal::create([
           'date' => $request->date,
           'meal_order' => $request->meal_order,
           'user_id' => auth()->id(),
        ]);

        return $meal;
    }

    public function deleteMeal(RequiredIdRequest $request)
    {
        $meal = Meal::findOrFail($request->id)->delete();

        return $meal;
    }

    public function addProductIntoMeal(AddProductToMealRequest $request)
    {
        $meal = Meal::with('products')
            ->where('date', $request->date)
            ->where('meal_order', $request->meal_order)
            ->first();

        $this->productService->addProductOrIncreaseCountIntoMeal($request->product_id, $request->weight, $meal);
    }

    public function show(RequiredDateRequest $request)
    {
        $meal = Meal::where('date', $request->date)
            ->where('user_id', auth()->id())
            ->get();

        return MealResource::collection($meal);
    }

    public function deleteProduct(DeleteProductRequest $request)
    {
        try {
            MealProduct::where('meal_id', $request->meal_id)
                ->where('product_id', $request->product_id)
                ->where('weight_product', $request->weight_product)
                ->delete();

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function increaseCountProduct(ChangeCountProductRequest $request)
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

    public function decreaseCountProduct(ChangeCountProductRequest $request)
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

    public function updateWeightProduct(UpdateWeightProductRequest $request)
    {
        try {
            $mealProduct = MealProduct::where('meal_id', $request->meal_id)
                ->where('product_id', $request->product_id)
                ->where('weight_product', $request->weight_product)
                ->first();
            $mealProduct->weight_product = $request->changed_weight;
            $mealProduct->save();

            return response()->json(['message' => 'Product changed weight successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
