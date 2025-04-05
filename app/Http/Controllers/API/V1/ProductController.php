<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\General\Authorize\RequiredIdRequest;
use App\Http\Services\Meal\Contracts\ProductServiceInterface;

class ProductController extends Controller
{
    public ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index(): JsonResponse
    {
        $products = Product::orderBy('created_at', 'asc')
            ->where('user_id', auth()->id())
            ->orWhere('is_public', true)
            ->get();

        return response()->json($products);
    }

    public function search(string $name): JsonResponse
    {
        $products = Product::where('name', 'like', '%' . $name . '%')
            ->orWhere('description', 'like', '%' . $name . '%')
            ->get();

        return response()->json($products);
    }

    public function create(ProductCreateRequest $request): JsonResponse
    {
        try {
            $formattedProductData = $this->productService->getFormattedProductData($request);
            $product = Product::create($formattedProductData);

            return response()->json($product, 200);
        } catch (\Exception $e) {
            Log::error(__('log_error.creating_product') . $e->getMessage());
            return response()->json(['message' => __('errors.creating_the_product')], 500);
        }
    }

    public function update(ProductUpdateRequest $request): JsonResponse
    {
        try {
            $formattedProductData = $this->productService->getFormattedProductData($request);
            $product = Product::findOrFail($request->integer('id'));
            $product->update($formattedProductData);

            return response()->json($product);
        } catch (\Exception $e) {
            Log::error(__('log_error.updating_product') . $e->getMessage());
            return response()->json(['message' => __('errors.updating_the_product')], 500);
        }
    }

    public function delete(RequiredIdRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->integer('id'));
        $product->delete();

        return response()->json(['message' => __('messages.product_deleted_successfully')]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }
}
