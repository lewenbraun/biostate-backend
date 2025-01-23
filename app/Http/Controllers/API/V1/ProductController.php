<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\Meal\ProductService;
use App\Http\Requests\General\RequiredIdRequest;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;

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
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($product, 200);
    }

    public function update(ProductUpdateRequest $request): JsonResponse
    {
        try {
            $formattedProductData = $this->productService->getFormattedProductData($request);
            $product = Product::findOrFail($request->id);
            $product->update($formattedProductData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($product);
    }

    public function delete(RequiredIdRequest $request): JsonResponse
    {
        try {
            $product = Product::findOrFail($request->id);
            $product->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }
}
