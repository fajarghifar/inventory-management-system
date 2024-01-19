<?php

namespace App\Http\Requests\Purchase;

use App\Enums\PurchaseStatus;
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
            'supplier_id'   => 'required',
            'date'          => 'required|string',
            'total_amount'  => 'required|numeric',
        ];
    }

}
