<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class AccountStoreRequest extends FormRequest
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
            'tax_office' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'authorized_name' => 'required|string|max:255',
            'authorized_phone' => 'required|string|max:20',
            'authorized_email' => 'required|email|max:255',
            'note' => 'nullable|string',
            'category_id' => 'required|exists:account_categories,id|integer',
        ];
    }

    /**
     * Alanlara ait hata mesajlarını özelleştirmek için kullanılır.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Firma adı boş bırakılamaz.',
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
            'tax_office.max' => 'Vergi dairesi en fazla 255 karakter olmalıdır.',
            'contact_name.required' => 'Kişi adı boş bırakılamaz.',
            'contact_name.string' => 'Lütfen geçerli bir kişi adı giriniz.',
            'contact_name.max' => 'Kişi adı en fazla 255 karakter olmalıdır.',
            'contact_phone.required' => 'Telefon boş bırakılamaz.',
            'contact_phone.string' => 'Lütfen geçerli bir telefon giriniz.',
            'contact_phone.max' => 'Telefon en fazla 12 karakter olmalıdır.',
            'contact_email.required' => 'E-posta boş bırakılamaz.',
            'contact_email.email' => 'Lütfen geçerli bir e-posta giriniz.',
            'contact_email.max' => 'E-posta adresi en fazla 255 karakter olmalıdır.',
            'authorized_name.required' => 'Kişi adı boş bırakılamaz.',
            'authorized_name.string' => 'Lütfen geçerli bir kişi adı giriniz.',
            'authorized_name.max' => 'Kişi adı en fazla 255 karakter olmalıdır.',
            'authorized_phone.required' => 'Telefon boş bırakılamaz.',
            'authorized_phone.string' => 'Lütfen geçerli bir telefon giriniz.',
            'authorized_phone.max' => 'Telefon en fazla 12 karakter olmalıdır.',
            'authorized_email.required' => 'E-posta boş bırakılamaz.',
            'authorized_email.email' => 'Lütfen geçerli bir e-posta giriniz.',
            'authorized_email.max' => 'E-posta adresi en fazla 255 karakter olmalıdır.',
            'category_id.required' => 'Kategori boş bırakılamaz.',
            'category_id.exists' => 'Seçilen kategori mevcut değil.'
        ];
    }
}
