<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DueOrderController extends Controller
{
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = Order::where('due', '>', '0')
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('due.index', [
            'orders' => $orders
        ]);
    }

    public function show(Order $order)
    {
//        $details = OrderDetails::with(['product'])
//            ->where('order_id', $order)
//            ->orderBy('id')
//            ->get();
//
//        return view('due.show', [
//            'order' => $order,
//            'details' => $details,
//        ]);

        return view('due.show', [
           'order' => $order->load('details')
        ]);
    }

    public function edit(Order $order)
    {
        return view('due.edit', [
            'order' => $order->load(['customer', 'details'])
        ]);
    }

    public function update(Order $order, Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
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
