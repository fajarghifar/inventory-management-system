<?php

namespace App\Http\Requests\Order;

use Illuminate\Support\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Http\FormRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required',
            'payment_type' => 'required',
            'pay' => 'required|numeric'
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'order_date' => Carbon::now()->format('Y-m-d'),
            'order_status' => 'pending',
            'total_products' => Cart::count(),
            'sub_total' => Cart::subtotal(),
            'vat' => Cart::tax(),
            'total' => Cart::total(),
            'invoice_no' => IdGenerator::generate([
                'table' => 'orders',
                'field' => 'invoice_no',
                'length' => 10,
                'prefix' => 'INV-'
            ]),
            //'due' => ((int)Cart::total()) - ((int)$this->pay)
            'due' => (Cart::total() - $this->pay)
        ]);
    }
}
