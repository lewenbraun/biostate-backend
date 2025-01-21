<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function profileData(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'name' => $user->name,
            'weight' => $user->weight,
        ]);
    }

    public function maxNutrients(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'calories' => $user->calories,
            'proteins' => $user->proteins,
            'carbs' => $user->carbs,
            'fats' => $user->fats,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();

        $user->update([
            'name' => $request->name,
            'weight' => $request->weight,
            'calories' => $request->calories,
            'proteins' => $request->proteins,
            'carbs' => $request->carbs,
            'fats' => $request->fats,
        ]);
        return response()->json($user);
    }
}
