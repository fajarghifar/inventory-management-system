<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DueOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('due', '>', '0')
            ->latest()
            ->with('customer')
            ->paginate();

        return view('due.index', [
            'orders' => $orders
        ]);
    }

    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'details'])->get();

        return view('due.show', [
           'order' => $order
        ]);
    }

    public function edit(Order $order)
    {
        $order->loadMissing(['customer', 'details'])->get();

        $customers = Customer::select(['id', 'name'])->get();

        return view('due.edit', [
            'order' => $order,
            'customers' => $customers
        ]);
    }

    public function update(Order $order, Request $request)
    {
        $rules = [
            'pay' => 'required|numeric'
        ];

        $validatedData = $request->validate($rules);

        $mainPay = $order->pay;
        $mainDue = $order->due;

        $paidDue = $mainDue - $validatedData['pay'];
        $paidPay = $mainPay + $validatedData['pay'];

        $order->update([
            'due' => $paidDue,
            'pay' => $paidPay
        ]);

        return redirect()
            ->route('due.index')
            ->with('success', 'Due amount has been updated!');
    }
}
