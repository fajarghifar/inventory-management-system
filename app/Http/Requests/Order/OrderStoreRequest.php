<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use App\Helpers\IdGenerator;
use Cart; // ✅ This uses Darryldecode\Cart\Facades\CartFacade (defined in config/app.php)

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'   => 'required|exists:customers,id',
            'payment_type'  => 'required|string',
            'pay'           => 'required|numeric|min:0',
        ];
    }

    public function prepareForValidation(): void
    {
        $instance = Cart::session('order'); // ✅ required for darryldecode/cart

        $this->merge([
            'order_date'     => Carbon::now()->format('Y-m-d'),
            'order_status'   => OrderStatus::PENDING->value,
            'total_products' => $instance->getContent()->count(),
            'sub_total'      => $instance->getSubTotal(),
            'vat'            => $instance->getCondition('VAT')?->getCalculatedValue($instance->getSubTotal()) ?? 0,
            'total'          => $instance->getTotal(),
            'invoice_no'     => IdGenerator::generate([
                'table'  => 'orders',
                'field'  => 'invoice_no',
                'length' => 10,
                'prefix' => 'INV-',
            ]),
            'due'            => $instance->getTotal() - $this->pay,
        ]);
    }
}
