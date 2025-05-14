<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class TechnicalServiceStoreRequest extends FormRequest
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
            'cari_ids' => 'required|array',
            'cari_ids.*' => 'integer',
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'serial_number' => 'required|max:255',
            'fault_information' => 'required|max:255',
            'name' => 'required|max:255',
            'phone' => 'required|max:12',
            'note' => 'nullable',
            'technical_service_company' => 'required|string|max:255',
            'shipment_delivery_number' => 'required|string|max:50',
            'shipping_company_sent' => 'required|string|max:255',
            'shipment_cargo_number' => 'required|string|max:50',
            'status' => 'integer',
            'date_created'=>'required'
        ];
    }

    public function messages(): array
    {
        return [
            'cari_ids.required' => 'Cari bilgisi boş bırakılamaz.',
            'cari_ids.array' => 'Cari bilgisi bulunamadı.',
            'cari_ids.integer' => 'Cari bilgisi bulunamadı.',
            'product_id.required' => 'Ürün bilgisi boş bırakılamaz.',
            'product_id.exists' => 'Ürün bilgisi bulunamadı.',
            'user_id.required' => 'Personel bilgisi boş bırakılamaz.',
            'user_id.exists' => 'Personel bilgisi bulunamadı.',
            'serial_number.required' => 'Seri numarası boş bırakılamaz.',
            'serial_number.max' => 'Seri numarası en fazla 255 karakter olmalıdır.',
            'name.required' => 'Ad soyad boş bırakılamaz.',
            'name.max' => 'Ad soyad en fazla 255 karakter olmalıdır.',
            'phone.required' => 'Telefon numarası boş bırakılamaz.',
            'phone.max' => 'Telefon numarası en fazla 12 karakter olmalıdır.', 
            'technical_service_company.required' => 'Teknik servis firması boş bırakılamaz.',
            'technical_service_company.string' => 'Teknik servis firması yalnızca metin içerebilir.',
            'technical_service_company.max' => 'Teknik servis firması en fazla 255 karakter olabilir.',
            'shipment_delivery_number.required' => 'Gönderi irsaliye numarası boş bırakılamaz.',
            'shipment_delivery_number.string' => 'Gönderi irsaliye numarası yalnızca metin içerebilir.',
            'shipment_delivery_number.max' => 'Gönderi irsaliye numarası en fazla 50 karakter olabilir.',
            'shipping_company_sent.required' => 'Gönderilen kargo firması zorunludur.',
            'shipping_company_sent.string' => 'Gönderilen kargo firması yalnızca metin içerebilir.',
            'shipping_company_sent.max' => 'Gönderilen kargo firması en fazla 255 karakter olabilir.',
            'shipment_cargo_number.required' => 'Gönderi kargo numarası zorunludur.',
            'shipment_cargo_number.string' => 'Gönderi kargo numarası yalnızca metin içerebilir.',
            'shipment_cargo_number.max' => 'Gönderi kargo numarası en fazla 50 karakter olabilir.',
            'status.integer' => 'Durum boş bırakılamaz.'
        ];
    }
}
