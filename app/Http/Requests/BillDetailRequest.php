<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillDetailRequest extends FormRequest
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
            'menu_id' => 'required|string|exists:menus,id',
            'qty' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
            'bill_detail_toppings' => 'nullable|array',
        ];
    }
}
