<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderPendingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = Order::where('order_status', 'pending')
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('orders.pending-orders', [
            'orders' => $orders
        ]);
    }
}
