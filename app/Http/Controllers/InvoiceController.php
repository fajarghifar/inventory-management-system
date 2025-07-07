<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Models\Customer;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class InvoiceController extends Controller
{
    public function create(StoreInvoiceRequest $request)
    {
        // Fetch the customer by ID from the request
        $customer = Customer::findOrFail($request->get('customer_id'));

        // Get cart contents using Darryldecode for the logged-in user
        $carts = Cart::session(auth()->id())->getContent();

        // Return the invoice view with customer and cart data
        return view('invoices.index', [
            'customer' => $customer,
            'carts'    => $carts,
        ]);
    }
}
