<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

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
            'pay' => 'required|numeric',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'order_date' => Carbon::now()->format('Y-m-d'),
            'order_status' => OrderStatus::PENDING->value,
            'total_products' => Cart::instance('order')->count(),
            'sub_total' => Cart::instance('order')->subtotal(),
            'vat' => Cart::instance('order')->tax(),
            'total' => Cart::instance('order')->total(),
            'invoice_no' => IdGenerator::generate([
                'table' => 'orders',
                'field' => 'invoice_no',
                'length' => 10,
                'prefix' => 'INV-',
            ]),
            'due' => (Cart::instance('order')->total() - $this->pay),
        ]);
    }
}
