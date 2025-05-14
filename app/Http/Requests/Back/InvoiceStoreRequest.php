<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
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
            'account_id' => 'required|exists:accounts,id',
            'currency' => 'required|max:5',
            'price' => 'required|numeric|min:0',
            'invoice_number' => 'required|max:255',
            'invoice_date' => 'required|date',
            'expiry_date' => 'required|date',
            'discount' => 'required|integer',
            'tax' => 'required|integer',
            'type' => 'required|integer',
            'status' => 'required|integer',
            'items' => 'required|array',
            'items.*.currency' => 'required|string|max:255',
            'items.*.exchange_rate' => 'required|numeric|min:0',
            'items.*.equivalent' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0'
            
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'Cari boş bırakılamaz.',
            'account_id.max' => 'Cari bulunamadı.',
            'currency.required' =>  'Para birimi boş bırakılamaz.',
            'currency.max' => 'Lütfen geçerli bir parabirimi seçiniz.',
            'invoice_number.required' => 'Fatura no boş bırakılamaz.',
            'invoice_number.max' => 'Fatura no en fazla 255 karakter olmalıdır.',
            'invoice_date.required' =>  'Fatura Tarihi boş bırakılamaz.',
            'invoice_date.date' => 'Lütfen geçerli bir tarih giriniz.',
            'expiry_date.required' =>  'Vade Tarihi boş bırakılamaz.',
            'expiry_date.date' => 'Lütfen geçerli bir tarih giriniz.',
            'discount.required' => 'İndirim boş bırakılamaz.',
            'discount.integer' => 'Lütfen geçerli bir indirim miktarı giriniz.',
            'tax.required' => 'Vergi boş bırakılamaz.',
            'tax.integer' => 'Lütfen geçerli bir vergi miktarı giriniz.',
            'type.required' => 'Fatura tipi boş bırakılamaz.',
            'type.integer' => 'Lütfen geçerli bir fatura tipi seçiniz.',
            'status.required' => 'Durum boş bırakılamaz.',
            'status.integer' => 'Lütfen geçerli bir durum seçiniz.'
        ];
    }
}
