<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'profileData.name' => 'string|required',
            'profileData.weight' => 'nullable|decimal:0,3',
            'maxNutrients.calories' => 'nullable|integer',
            'maxNutrients.proteins' => 'nullable|integer',
            'maxNutrients.carbs' => 'nullable|integer',
            'maxNutrients.fats' => 'nullable|integer',
        ];
    }
}
