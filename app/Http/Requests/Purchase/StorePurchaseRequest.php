<?php

namespace App\Http\Requests\Purchase;

use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id'       => 'required',
            'purchase_date'     => 'required|string',
            'total_amount'      => 'required|numeric',
            'purchase_status'   => 'required',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'purchase_no' => IdGenerator::generate([
                'table' => 'purchases',
                'field' => 'purchase_no',
                'length' => 10,
                'prefix' => 'PRS-'
            ]),
            'purchase_status'   => 0, // 0 = pending, 1 = approved
            'created_by'        => auth()->user()->id,
        ]);
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Supplier is required',
        ];
    }
}
