<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

class InvoiceController extends Controller
{
    public function create(StoreInvoiceRequest $request, Customer $customer)
    {
        $customer = Customer::where('id', $request->get('customer_id'))
            ->first();

        $carts = Cart::content();

        return view('pos.create', [
            'customer' => $customer,
            'carts' => $carts
        ]);
    }
}
