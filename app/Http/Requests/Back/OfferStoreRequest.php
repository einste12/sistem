<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class OfferStoreRequest extends FormRequest
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
            'account_category_id' => 'required|exists:account_categories,id',
            'account_id' => 'required|exists:accounts,id',
            'expiry' => 'required|integer',
            'offer_name' => 'required|max:255',
            'note' => 'nullable|max:255',
            'status' => 'required',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'account_category_id.required' => 'Cari kategori boş bırakılamaz.',
            'account_category_id.exists' => 'Cari kategori bulunamadı.',
            'account_id.required' => 'Cari bilgisi boş bırakılamaz.',
            'account_id.exists' => 'Cari bilgisi bulunamadı.',
            'expiry.required' => 'Vade boş bırakılamaz.',
            'expiry.integer' => 'Lütfen geçerli bir vade giriniz.',
            'offer_name.required' => 'Bu alan boş bırakılamaz.',
            'offer_name.max' => 'Bu alan en fazla 255 karakter olmalıdır.',
            'note.max' => 'Not en fazla 255 karakter olmalıdır.'
        ];
    }
}
