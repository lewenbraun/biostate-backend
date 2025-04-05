<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;

class UserController extends Controller
{
    public function profileData(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        return response()->json([
            'name' => $user->name,
            'weight' => $user->weight,
        ]);
    }

    public function maxNutrients(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        return response()->json([
            'calories' => $user->calories,
            'proteins' => $user->proteins,
            'carbs' => $user->carbs,
            'fats' => $user->fats,
        ]);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $user->update([
            'name' => $request->profileData['name'],
            'weight' => $request->profileData['weight'],
            'calories' => $request->maxNutrients['calories'],
            'proteins' => $request->maxNutrients['proteins'],
            'carbs' => $request->maxNutrients['carbs'],
            'fats' => $request->maxNutrients['fats'],
        ]);
        return response()->json($user);
    }
}
