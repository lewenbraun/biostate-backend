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
        try {
            $products = Product::orderBy('created_at', 'asc')
                ->where('user_id', auth()->id())
                ->get();

            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching products.'], 500);
        }
    }

    public function search(string $name): JsonResponse
    {
        try {
            $products = Product::where('name', 'like', '%' . $name . '%')
                ->orWhere('description', 'like', '%' . $name . '%')
                ->get();

            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error searching products: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while searching for products.'], 500);
        }
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
        try {
            $product = Product::findOrFail($request->id);
            $product->delete();

            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting the product.'], 500);
        }
    }

    public function show(Product $product): JsonResponse
    {
        try {
            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('Error fetching product details: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching the product details.'], 500);
        }
    }
}
