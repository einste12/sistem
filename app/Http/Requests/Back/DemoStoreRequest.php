<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class DemoStoreRequest extends FormRequest
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
            'company_name' => 'required|string|max:255',
            'company_authorized' => 'required|string|max:255',
            'company_contact' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'incoming_delivery_number' => 'required|numeric',
            'outgoing_delivery_number' => 'required|numeric',
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'items.*.categorie_id' => 'required|exists:categories,id',
            'items.*.brand_id' => 'required|exists:brands,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.serial_number' => 'nullable|string|max:255',
            'items.*.date' => 'nullable|date',
            'items.*.demo_date' => 'nullable|date',
        ];
    }

    /**
     * Get the custom error messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'company_name.required' => 'Firma adı zorunludur.',
            'company_authorized.required' => 'Firma yetkilisi zorunludur.',
            'company_contact.required' => 'Firma iletişimi zorunludur.',
            'company_email.required' => 'E-posta adresi zorunludur.',
            'company_email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'incoming_delivery_number.required' => 'Gelen irsaliye numarası zorunludur.',
            'outgoing_delivery_number.required' => 'Giden irsaliye numarası zorunludur.',
            'name.required' => 'Ad ve soyad zorunludur.',
            'phone.required' => 'Telefon numarası zorunludur.',
            'items.*.categorie_id.required' => 'Kategori seçimi zorunludur.',
            'items.*.brand_id.required' => 'Marka seçimi zorunludur.',
            'items.*.product_id.required' => 'Ürün seçimi zorunludur.',
            'items.*.quantity.required' => 'Miktar zorunludur.',
            'items.*.quantity.min' => 'Miktar 1\'den küçük olamaz.',
            'items.*.serial_number.max' => 'Seri numarası 255 karakteri aşamaz.',
            'items.*.date.date' => 'Geçerli bir tarih giriniz.',
            'items.*.demo_date.date' => 'Geçerli bir demo tarihi giriniz.',
        ];
    }
}
