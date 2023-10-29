<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_image' => 'image|file|max:2048',
            'product_name' => 'required|string',
            'category_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'stock' => 'required|integer',
            'buying_price' => 'required|integer',
            'selling_price' => 'required|integer',
        ];
    }
}
