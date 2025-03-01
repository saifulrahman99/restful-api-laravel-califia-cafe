<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|string|exists:categories,id',
            'name' => 'required|string|max:100|unique:categories,name',
        ];
    }
    public function messages(): array
    {
        return [
            'id.required' => 'Id kategori tidak boleh kosong.',
            'id.string' => 'Id kategori harus berupa teks.',
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
            'name.unique' => 'Nama kategori sudah ada, gunakan nama lain.',
        ];
    }
}
