<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(ProductCreateRequest $request): JsonResponse
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id['id'],
            'image' => $request->image,
            'calories' => $request->calories,
            'proteins' => $request->proteins,
            'carbs' => $request->carbs,
            'fats' => $request->fats,
        ]);


        if ($product) {
            return response()->json($product, 201);
        } else {
            return response()->json(['message' => 'Failed to create product'], 500);
        }
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
