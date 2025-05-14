<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Sub_categorie;
use App\Models\Brand;
use App\Models\Category;


class ProductStoreRequest extends FormRequest
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
            'title' => 'required|max:255',
            'code' => 'required|max:255|unique:products,code',
            'stock_code' => 'required|max:255|unique:products,stock_code',
            'manufacturer_code' => 'required|max:255',
            'description' => 'required|max:255',
            'keywords' => 'required|max:255',
            'meta_title' => 'required|max:255',
            'meta_keywords' => 'required|max:255',
            'meta_description' => 'required|max:255',
            'price_1' => 'required|numeric|min:0',
            'price_2' => 'required|numeric|min:0',
            'price_3' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'required|exists:brands,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Ürün başlığı boş bırakılamaz.',
            'title.max' => 'Ürün başlığı en fazla 255 karakter olmalıdır.',
            'code.required' => 'Ürün kodu boş bırakılamaz.',
            'code.max' => 'Ürün kodu en fazla 255 karakter olmalıdır.',
            'code.unique' => 'Bu ürün kodu zaten mevcut.',
            'stock_code.required' => 'Stok kodu boş bırakılamaz.',
            'stock_code.max' => 'Stok kodu en fazla 255 karakter olmalıdır.',
            'stock_code.unique' => 'Bu stok kodu zaten mevcut.',
            'manufacturer_code.required' => 'Üretici kodu boş bırakılamaz.',
            'manufacturer_code.max' => 'Üretici kodu en fazla 255 karakter olmalıdır.',
            'description.required' => 'Açıklama boş bırakılamaz.',
            'description.max' => 'Açıklama en fazla 255 karakter olmalıdır.',
            'keywords.required' => 'Anahtar kelimeler boş bırakılamaz.',
            'keywords.max' => 'Anahtar kelimeler en fazla 255 karakter olmalıdır',
            'meta_title.required' => 'Meta başlığı boş bırakılamaz.',
            'meta_title.max' => 'Meta başlığı en fazla 255 karakter olmalıdır.',
            'meta_keywords.required' => 'Meta anahtar kelimeler boş bırakılamaz.',
            'meta_keywords.max' => 'Meta anahtar kelimeler en fazla 255 karakter olmalıdır.',
            'meta_description.required' => 'Meta açıklaması boş bırakılamaz.',
            'meta_description.max' => 'Meta açıklaması en fazla 255 karakter olmalıdır.', 
            'price_1.required' => '1. fiyat boş bırakılamaz.',
            'price_1.numeric' => '1. fiyat sayısal bir değer olmalıdır.',
            'price_2.required' => '2. fiyat boş bırakılamaz.',
            'price_2.numeric' => '2. fiyat sayısal bir değer olmalıdır.',
            'price_3.required' => '3. fiyat boş bırakılamaz.',
            'price_3.numeric' => '3. fiyat sayısal bir değer olmalıdır.',
            'category_id.required' => 'Kategori boş bırakılamaz.',
            'category_id.exists' => 'Seçilen kategori geçerli değil.',
            'sub_category_id.required' => 'Kategori boş bırakılamaz.',
            'subcategory_id.exists' => 'Seçilen kategori geçerli değil.',
            'brand_id.required' => 'Marka boş bırakılamaz.',
            'brand_id.exists' => 'Seçilen marka geçerli değil.'
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            $category = Category::query()->where('id', $this->category_id)->first();
            if ($category) {
                $sub_categories = Sub_categorie::query()->where('status', 1)->where('category_id',$category->id)->get();
                $brands = Brand::query()->where('status', 1)->where('category_id',$category->id)->get();
                return redirect()->back()->with('sub_categories', $sub_categories)
                ->with('brands', $brands);
            }
        }
    }
}
