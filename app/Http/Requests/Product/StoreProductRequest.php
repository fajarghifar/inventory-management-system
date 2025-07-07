<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Helpers\IdGenerator;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_image'     => 'nullable|image|file|max:2048',
            'name'              => 'required|string|max:255',
            'slug'              => [
                'required',
                Rule::unique('products')->ignore($this->product),
            ],
            'category_id'       => 'required|integer|exists:categories,id',
            'unit_id'           => 'required|integer|exists:units,id',
            'quantity'          => 'required|integer|min:0',
            'buying_price'      => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0|gt:buying_price',
            'quantity_alert'    => 'required|integer|min:0',
            'tax'               => 'nullable|numeric|min:0|max:100',
            'tax_type'          => 'nullable|integer|in:0,1',
            'notes'             => 'nullable|string|max:1000',
        ];
    }

    protected function prepareForValidation(): void
    {
        $slug = Str::slug($this->name, '-');

        $code = IdGenerator::generate([
            'table' => 'products',
            'field' => 'code',
            'length' => 4,
            'prefix' => 'PC'
        ]);

        $this->merge([
            'slug' => $slug,
            'code' => $code
        ]);
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'unit_id' => 'unit',
            'quantity_alert' => 'alert quantity',
            'buying_price' => 'purchase price',
            'selling_price' => 'sale price',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'A product with this name already exists',
            'selling_price.gt' => 'Selling price must be greater than buying price',
            'category_id.exists' => 'The selected category is invalid',
            'unit_id.exists' => 'The selected unit is invalid',
        ];
    }
}
