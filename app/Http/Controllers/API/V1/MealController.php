<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Models\Meal;
use App\Models\MealProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
use App\Http\Services\Meal\Contracts\ProductServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MealController extends Controller
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function createMeal(CreateMealRequest $request): JsonResponse
    {
        $meal = Meal::create([
            'date' => $request->date,
            'meal_order' => $request->meal_order,
            'user_id' => auth()->id(),
        ]);

        return response()->json($meal);
    }

    public function deleteMeal(RequiredIdRequest $request): JsonResponse
    {
        $meal = Meal::findOrFail($request->id)->delete();

        return response()->json($meal);
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
        $meal = Meal::where('date', $request->date)
            ->where('user_id', auth()->id())
            ->get();

        return MealResource::collection($meal);
    }

    public function deleteProduct(DeleteProductRequest $request): JsonResponse
    {
        MealProduct::where('meal_id', $request->meal_id)
            ->where('product_id', $request->product_id)
            ->where('weight_product', $request->weight_product)
            ->delete();

        return response()->json(['message' => 'Product deleted successfully']);
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
        $mealProduct = MealProduct::where('meal_id', $request->meal_id)
            ->where('product_id', $request->product_id)
            ->where('weight_product', $request->weight_product)
            ->first();

        $mealProduct->weight_product = $request->changed_weight;
        $mealProduct->save();

        return response()->json(['message' => 'Product weight updated successfully.']);
    }
}
