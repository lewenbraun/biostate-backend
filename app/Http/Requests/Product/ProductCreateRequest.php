<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
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
            'name' => 'string|required',
            'description' => 'string',
            'price' => 'integer',
            'category_id' => 'integer|exists:categories,id',
            'for_weight' => 'nullable|float|required_with:calories,proteins,carbs,fats',
            'calories' => 'nullable|float',
            'proteins' => 'nullable|float',
            'carbs' => 'nullable|float',
            'fats' => 'nullable|float',
        ];
    }
}
