<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Aturan untuk invoice_number: satu-satunya yang berbeda antara create dan update. */
    abstract protected function invoiceNumberRules(): array;

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'invoice_number' => $this->invoiceNumberRules(),
            'purchase_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'notes' => ['nullable', 'string'],
            // 'status' ditangani controller (create: draft, update: dipertahankan)
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.selling_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please add at least one item.',
            'items.*.product_id.required' => 'Product is required.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
