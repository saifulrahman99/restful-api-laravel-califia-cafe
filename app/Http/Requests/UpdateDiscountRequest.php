<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'exists:discounts,id'],
            'name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'start_date' => ['required', 'date', 'after_or_equal:today', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:today', 'after:start_date'],
            'is_active' => 'required|boolean',
        ];
    }
}
