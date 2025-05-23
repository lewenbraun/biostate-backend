<?php

namespace App\Models;

use App\Models\Meal;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the meals associated with the Product
     *
     * @return HasOne<Meal, $this>
     */
    public function meals(): HasOne
    {
        return $this->hasOne(Meal::class);
    }
}
