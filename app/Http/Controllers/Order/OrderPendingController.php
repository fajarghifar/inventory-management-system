<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderPendingController extends Controller
{
    public function __invoke(Request $request)
    {
        $orders = Order::where('order_status', 'pending')
            ->latest()
            ->get();

        return view('orders.pending-orders', [
            'orders' => $orders
        ]);
    }
}
