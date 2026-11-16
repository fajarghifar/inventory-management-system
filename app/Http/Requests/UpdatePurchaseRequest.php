<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdatePurchaseRequest extends PurchaseRequest
{
    protected function invoiceNumberRules(): array
    {
        return [
            'nullable',
            'string',
            'max:255',
            Rule::unique('purchases', 'invoice_number')->ignore($this->route('purchase')),
        ];
    }
}
