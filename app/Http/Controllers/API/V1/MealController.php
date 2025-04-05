<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Models\Meal;
use App\Models\MealProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\Meal\MealResource;
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
            'date' => $request->date('date'),
            'meal_order' => $request->integer('meal_order'),
            'user_id' => auth()->id(),
        ]);

        return response()->json($meal);
    }

    public function deleteMeal(RequiredIdRequest $request): JsonResponse
    {
        $meal = Meal::findOrFail($request->integer('id'));
        $meal->delete();

        return response()->json(['message' => __('messages.meal_deleted_successfully')]);
    }

    public function addProductIntoMeal(AddProductToMealRequest $request): JsonResponse
    {
        try {
            $meal = Meal::with('products')
                ->where('date', $request->date('date'))
                ->where('meal_order', $request->integer('meal_order'))
                ->firstOrFail();

            $this->productService->addProductOrIncreaseCountIntoMeal(
                $request->integer('product_id'),
                $request->float('weight'),
                $meal
            );
            return response()->json(['message' => __('message.product_added_successfully')]);
        } catch (\Exception $e) {
            Log::error(__('log_error.adding_product_to_meal') . $e->getMessage());
            return response()->json(['message' => __('errors.adding_the_product')], 500);
        }
    }

    public function show(RequiredDateRequest $request): AnonymousResourceCollection
    {
        $meals = Meal::where('date', $request->date)
            ->where('user_id', auth()->id())
            ->get();

        return MealResource::collection($meals);
    }

    public function deleteProduct(DeleteProductRequest $request): JsonResponse
    {
        MealProduct::where('meal_id', $request->integer('meal_id'))
            ->where('product_id', $request->integer('product_id'))
            ->where('weight_product', $request->float('weight_product'))
            ->delete();

        return response()->json(['message' => __('messages.product_deleted_successfully')]);
    }

    public function increaseCountProduct(ChangeCountProductRequest $request): JsonResponse
    {
        try {
            $meal = Meal::findOrFail($request->integer('meal_id'));
            $product = $meal->products->findOrFail($request->integer('product_id'));
            $this->productService->increaseCountProduct($product);

            return response()->json(['message' => __('messages.product_count_increased_successfully')]);
        } catch (\Exception $e) {
            Log::error(__('log_error.increasing_product_count') . $e->getMessage());
            return response()->json(['message' => __('errors.increasing_product_count')], 500);
        }
    }

    public function decreaseCountProduct(ChangeCountProductRequest $request): JsonResponse
    {
        try {
            $meal = Meal::findOrFail($request->integer('meal_id'));
            $product = $meal->products->findOrFail($request->integer('product_id'));

            if ($product->getRelationValue('pivot')->count === 1) {
                $meal->products()->detach($request->product_id);
            } else {
                $this->productService->decreaseCountProduct($product);
            }

            return response()->json(['message' => __('messages.product_count_decreased_successfully')]);
        } catch (\Exception $e) {
            Log::error(__('log_error.decreasing_product_count') . $e->getMessage());
            return response()->json(['message' => __('errors.decreasing_product_count')], 500);
        }
    }

    public function updateWeightProduct(UpdateWeightProductRequest $request): JsonResponse
    {
        $mealProduct = MealProduct::where('meal_id', $request->integer('meal_id'))
            ->where('product_id', $request->integer('product_id'))
            ->where('weight_product', $request->float('weight_product'))
            ->firstOrFail();

        $mealProduct->weight_product = $request->float('changed_weight');
        $mealProduct->save();

        return response()->json(['message' => __('messages.product_weight_updated_successfully')]);
    }
}
