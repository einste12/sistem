<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
            'reference_account_id' => 'required|exists:accounts,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|numeric|digits_between:10,15',
            'customer_adress' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id',
            'items' => 'required|array|min:1',
            'items.*.categorie_id' => 'required|exists:categories,id',
            'items.*.brand_id' => 'required|exists:brands,id',
            'items.*.product_id' => 'required|exists:products,id',
            'note' => 'nullable|string|max:2000'
        ];
    }

    /**
     * Hata mesajlarını özelleştirir.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'reference_account_id.required' => 'Lütfen bir register firması seçin.',
            'reference_account_id.exists' => 'Seçilen register firması geçerli değil.',
            'customer_name.required' => 'Müşteri adını girmek zorunludur.',
            'customer_email.required' => 'Müşteri e-posta adresini girmek zorunludur.',
            'customer_phone.required' => 'Müşteri telefon numarasını girmek zorunludur.',
            'customer_phone.digits_between' => 'Lütfem geçerli bir telefon numarası giriniz.',
            'customer_phone.numeric' => 'Müşteri telefon numarası sadece rakamlardan oluşmalıdır.',
            'customer_adress.required' => 'Müşteri adresini girmek zorunludur.',
            'account_id.required' => 'Lütfen bir cari bilgisi seçin.',
            'items.required' => 'En az bir ürün eklemek zorundasınız.',
            'items.*.categorie_id.required' => 'Ürün kategorisi seçmek zorunludur.',
            'items.*.brand_id.required' => 'Ürün markası seçmek zorunludur.',
            'items.*.product_id.required' => 'Ürün seçmek zorunludur.'
        ];
    }
}
