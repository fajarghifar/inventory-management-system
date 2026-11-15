<?php

namespace App\Http\Requests;

class StorePurchaseRequest extends PurchaseRequest
{
    protected function invoiceNumberRules(): array
    {
        return ['nullable', 'string', 'max:255', 'unique:purchases,invoice_number'];
    }
}
