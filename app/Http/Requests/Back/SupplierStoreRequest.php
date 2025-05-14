<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class SupplierStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'adress' => 'required|string|max:255',
            'phone' => 'required|string|max:12',
            'email' => 'required|email|max:255',
            'tax_no' => 'required|string|max:20',
            'tax_office' => 'required|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Lütfen geçerli bir firma adı giriniz.',
            'name.max' => 'Firma adı en fazla 255 karakter olmalıdır.',
            'adress.required' => 'Adres boş bırakılamaz.',
            'adress.string' => 'Lütfen geçerli bir adres giriniz.',
            'adress.max' => 'Adres en fazla 255 karakter olmalıdır.',
            'phone.required' => 'Telefon boş bırakılamaz.',
            'phone.string' => 'Lütfen geçerli bir telefon giriniz.',
            'phone.max' => 'Telefon en fazla 12 karakter olmalıdır.',
            'email.required' => 'E-posta boş bırakılamaz.',
            'email.email' => 'Lütfen geçerli bir e-posta giriniz.',
            'email.max' => 'E-posta adresi en fazla 255 karakter olmalıdır.',
            'tax_no.required' => 'Vergi numarası boş bırakılamaz.',
            'tax_no.string' => 'Lütfen geçerli bir vergi numarası giriniz.',
            'tax_no.max' => 'Vergi numarası en fazla 20 karakter olmalıdır.',
            'tax_office.required' => 'Vergi dairesi boş bırakılamaz.',
            'tax_office.string' => 'Lütfen geçerli bir vergi dairesi giriniz.',
            'tax_office.max' => 'Vergi dairesi en fazla 255 karakter olmalıdır.'
        ];
    }
}
