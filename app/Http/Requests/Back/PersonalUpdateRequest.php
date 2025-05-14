<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class PersonalUpdateRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'required|email|unique:users,email,'.$this->id.',id|max:255',
            'adress' => 'nullable|string|max:500',
            'tc' => 'nullable|string|max:11|min:11',
            'note' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'password' => 'nullable|confirmed|string|min:8',
        ];
    }

    /**
     * Alan isimleri için özel hata mesajları.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'İsim alanı zorunludur.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmation' => 'Şifreler uyuşmuyor.',
            'tc.max' => 'TC numarası 11 karakter olmalıdır.',
            'tc.min' => 'TC numarası 11 karakter olmalıdır.',
            'department_id.exists' => 'Geçersiz bir departman seçtiniz.',
        ];
    }
}
