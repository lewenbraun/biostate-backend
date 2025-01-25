<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\Meal\ProductService;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\General\Authorize\RequiredIdRequest;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(): JsonResponse
    {
        $products = Product::orderBy('created_at', 'asc')
            ->where('user_id', auth()->id())
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
            Log::error('Error creating product: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating the product.'], 500);
        }
    }

    public function update(ProductUpdateRequest $request): JsonResponse
    {
        try {
            $formattedProductData = $this->productService->getFormattedProductData($request);
            $product = Product::findOrFail($request->id);
            $product->update($formattedProductData);

            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the product.'], 500);
        }
    }

    public function delete(RequiredIdRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }
}
