<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'email' => 'required|email|max:255',
            'password' => 'nullable|confirmed|max:255'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Ad soyad boş bırakılamaz.',
            'name.max' => 'Ad soyad en fazla 255 karakter olmalıdır.',
            'email.required' => 'E-posta adresi boş bırakılamaz.',
            'email.email' => 'Lütfen geçerli bir e-posta adresi giriniz.',
            'email.max' => 'E-posta adresi en fazla 255 karakter olmalıdır.',
            'password.confirmed' => 'Şifreler uyuşmuyor.',
            'password.max' => 'Şifre en fazla 255 karakter olmalıdır.'
        ];
    }
}
