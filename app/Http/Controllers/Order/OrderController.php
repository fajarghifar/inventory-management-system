<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        Cart::clear(); // ✅ Replaces Cart::instance('order')->destroy()

        return view('orders.create', [
            'carts' => Cart::getContent(), // ✅ Replaces Cart::content()
            'customers' => Customer::all(['id', 'name']),
            'products' => Product::with(['category', 'unit'])->get(),
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        $order = Order::create($request->all());

        // ✅ Create Order Details from Cart
        $contents = Cart::getContent();
        $orderDetails = [];

        foreach ($contents as $item) {
            $orderDetails[] = [
                'order_id'   => $order->id,
                'product_id' => $item->id,
                'quantity'   => $item->quantity,
                'unitcost'   => $item->price,
                'total'      => $item->getPriceSum(), // price * quantity
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        OrderDetails::insert($orderDetails);

        Cart::clear(); // ✅ Clear cart after order

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been created!');
    }

    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'details']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function update(Order $order, Request $request)
    {
        // Reduce stock of ordered products
        $products = OrderDetails::where('order_id', $order->id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update([
                    'quantity' => DB::raw('quantity - ' . $product->quantity),
                ]);
        }

        $order->update([
            'order_status' => OrderStatus::COMPLETE,
        ]);

        return redirect()
            ->route('orders.complete')
            ->with('success', 'Order has been completed!');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been deleted!');
    }

    public function downloadInvoice($orderId)
    {
        $order = Order::with(['customer', 'details'])->findOrFail($orderId);

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }
}
