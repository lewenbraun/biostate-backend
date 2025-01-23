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
            'name' => 'string|required',
            'weight' => 'nullable|decimal:0,3',
            'calories' => 'nullable|integer',
            'proteins' => 'nullable|integer',
            'carbs' => 'nullable|integer',
            'fats' => 'nullable|integer',
        ];
    }
}
