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
            'description' => 'nullable|string',
            'price' => 'nullable|integer',
            'category_id' => 'integer|exists:categories,id',
            'weight_for_features' => 'nullable|decimal:0,3|required_with:calories,proteins,carbs,fats',
            'calories' => 'nullable|decimal:0,3',
            'proteins' => 'nullable|decimal:0,3',
            'carbs' => 'nullable|decimal:0,3',
            'fats' => 'nullable|decimal:0,3',
        ];
    }
}
