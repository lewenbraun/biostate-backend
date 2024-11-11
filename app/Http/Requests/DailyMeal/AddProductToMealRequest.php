<?php

namespace App\Http\Requests\DailyMeal;

use Illuminate\Foundation\Http\FormRequest;

class AddProductToMealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer',
            'date' => 'required|date',
            'meal_order' => 'required|integer',
        ];
    }
}
