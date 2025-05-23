<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\MealProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MealProduct extends Model
{
    /** @use HasFactory<MealProductFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $table = 'meal_product';
}
