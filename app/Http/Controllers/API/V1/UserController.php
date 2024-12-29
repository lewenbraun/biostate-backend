<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function update(Request $request)
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
