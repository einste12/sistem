<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class OfferUpdateRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'product_name' => 'required|max:255',
            'product_price' => 'required',
            'discount' => 'required|integer',
            'expiry' => 'required|integer',
            'offer_name' => 'required|max:255',
            'note' => 'nullable|max:255',
            'status' => 'required|max:255'
        ];
    }
}
