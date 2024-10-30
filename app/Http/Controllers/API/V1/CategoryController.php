<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $category->update($request->all());
        return response()->json($category);
    }
}
