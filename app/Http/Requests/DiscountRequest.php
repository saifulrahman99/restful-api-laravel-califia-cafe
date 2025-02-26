<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'start_date' => ['required', 'date', 'after_or_equal:today', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:today', 'after:start_date'],
            'is_active' => 'required|boolean',
        ];
    }
}
