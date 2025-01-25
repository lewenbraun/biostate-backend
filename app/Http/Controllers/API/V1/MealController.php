<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Meal;
use App\Models\MealProduct;
use Illuminate\Http\JsonResponse;
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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class MealController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function createMeal(CreateMealRequest $request): JsonResponse
    {
        try {
            $meal = Meal::create([
                'date' => $request->date,
                'meal_order' => $request->meal_order,
                'user_id' => auth()->id(),
            ]);
            return response()->json($meal);
        } catch (\Exception $e) {
            Log::error('Error creating meal: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating the meal.'], 500);
        }
    }

    public function deleteMeal(RequiredIdRequest $request): JsonResponse
    {
        try {
            $meal = Meal::findOrFail($request->id)->delete();
            return response()->json($meal);
        } catch (\Exception $e) {
            Log::error('Error deleting meal: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting the meal.'], 500);
        }
    }

    public function addProductIntoMeal(AddProductToMealRequest $request): JsonResponse
    {
        try {
            $meal = Meal::with('products')
                ->where('date', $request->date)
                ->where('meal_order', $request->meal_order)
                ->first();

            $this->productService->addProductOrIncreaseCountIntoMeal($request->product_id, $request->weight, $meal);
            return response()->json(['message' => 'Product added successfully.']);
        } catch (\Exception $e) {
            Log::error('Error adding product to meal: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while adding the product.'], 500);
        }
    }

    public function show(RequiredDateRequest $request): AnonymousResourceCollection
    {
        try {
            $meal = Meal::where('date', $request->date)
                ->where('user_id', auth()->id())
                ->get();

            return MealResource::collection($meal);
        } catch (\Exception $e) {
            Log::error('Error fetching meals: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching meals.'], 500);
        }
    }

    public function deleteProduct(DeleteProductRequest $request): JsonResponse
    {
        try {
            MealProduct::where('meal_id', $request->meal_id)
                ->where('product_id', $request->product_id)
                ->where('weight_product', $request->weight_product)
                ->delete();

            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting the product.'], 500);
        }
    }

    public function increaseCountProduct(ChangeCountProductRequest $request): JsonResponse
    {
        try {
            $meal = Meal::findOrFail($request->meal_id);
            $product = $meal->products->findOrFail($request->product_id);
            $this->productService->increaseCountProduct($product);

            return response()->json(['message' => 'Product count increased successfully.']);
        } catch (\Exception $e) {
            Log::error('Error increasing product count: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while increasing product count.'], 500);
        }
    }

    public function decreaseCountProduct(ChangeCountProductRequest $request): JsonResponse
    {
        try {
            $meal = Meal::findOrFail($request->meal_id);
            $product = $meal->products->findOrFail($request->product_id);

            if ($product->pivot->count === 1) {
                $meal->products()->detach($request->product_id);
            } else {
                $this->productService->decreaseCountProduct($product);
            }

            return response()->json(['message' => 'Product count decreased successfully.']);
        } catch (\Exception $e) {
            Log::error('Error decreasing product count: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while decreasing product count.'], 500);
        }
    }

    public function updateWeightProduct(UpdateWeightProductRequest $request): JsonResponse
    {
        try {
            $mealProduct = MealProduct::where('meal_id', $request->meal_id)
                ->where('product_id', $request->product_id)
                ->where('weight_product', $request->weight_product)
                ->first();

            $mealProduct->weight_product = $request->changed_weight;
            $mealProduct->save();

            return response()->json(['message' => 'Product weight updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error updating product weight: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating product weight.'], 500);
        }
    }
}
