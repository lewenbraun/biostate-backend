<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\Meal\ProductService;
use App\Http\DTO\Meal\Product\ProductFeaturesDTO;
use App\Http\Requests\Product\ProductCreateRequest;

class ProductController extends Controller
{
    public $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(ProductCreateRequest $request): JsonResponse
    {
        try {
            $productFeatures = ProductFeaturesDTO::fromRequest($request);
            $formattedFeatures = $this->productService->formatWeight($productFeatures);

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'weight' => $request->weight,
                'category_id' => $request->category_id,
                'image' => $request->image,
                'calories' => $formattedFeatures->calories,
                'proteins' => $formattedFeatures->proteins,
                'carbs' => $formattedFeatures->carbs,
                'fats' => $formattedFeatures->fats,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($product, 200);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $product->update($request->all());
        return response()->json($product);
    }
}
