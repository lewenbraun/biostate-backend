<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => $request->image,
            'calories' => $request->calories,
            'proteins' => $request->proteins,
            'carbs' => $request->carbs,
            'fats' => $request->fats,
        ]);


        return response()->json($product, 201);
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
