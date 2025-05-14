<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class StockStoreRequest extends FormRequest
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
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'transaction_type' => 'required|integer',
            'quantity' => 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Tedarikci boş bırakılamaz.',
            'supplier_id.exists' => 'Tedarikci bulunamadı.',
            'product_id.required' => 'Ürün boş bırakılamaz.',
            'product_id.exists' => 'Ürün bulunamadı.',
            'quantity.required' => 'Adet boş bırakılamaz.',
            'quantity.integer' => 'Lütfen geçerli bir adet giriniz.'
        ];
    }
}
