<?php

namespace App\Http\Controllers\API\V1;

use App\Models\DailyMeal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DailyMealController extends Controller
{
    public function addProduct(Request $request)
    {
        DailyMeal::create([
            'product_id' => $request->product_id,
            'date' => $request->date,
            'meal_order' => $request->meal_order,
            'user_id' => auth()->id(),
        ]);
    }
}
