<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
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
            'name' => 'required|max:255',
            'status' => 'required|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Kategori adı boş bırakılamaz.',
            'name.max' => 'Kategori adı en fazla 255 karakter olmalıdır.',
            'status.required' => 'Durum boş bırakılamaz.'
        ];
    }
}
