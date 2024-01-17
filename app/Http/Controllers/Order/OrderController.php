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
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where("user_id", auth()->id())->count();

        return view('orders.index', [
            'orders' => $orders
        ]);
    }

    public function create()
    {
        $products = Product::where("user_id", auth()->id())->with(['category', 'unit'])->get();

        $customers = Customer::where("user_id", auth()->id())->get(['id', 'name']);

        $carts = Cart::content();

        return view('orders.create', [
            'products' => $products,
            'customers' => $customers,
            'carts' => $carts,
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'payment_type' => $request->payment_type,
            'pay' => $request->pay,
            'order_date' => Carbon::now()->format('Y-m-d'),
            'order_status' => OrderStatus::PENDING->value,
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
            'due' => (Cart::total() - $request->pay),
            "user_id" => auth()->id(),
            "uuid" => Str::uuid(),
        ]);

        // Create Order Details
        $contents = Cart::content();
        $oDetails = [];

        foreach ($contents as $content) {
            $oDetails['order_id'] = $order['id'];
            $oDetails['product_id'] = $content->id;
            $oDetails['quantity'] = $content->qty;
            $oDetails['unitcost'] = $content->price;
            $oDetails['total'] = $content->subtotal;
            $oDetails['created_at'] = Carbon::now();

            OrderDetails::insert($oDetails);
        }

        // Delete Cart Sopping History
        Cart::destroy();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been created!');
    }

    public function show($uuid)
    {
        $order = Order::where("uuid", $uuid)->firstOrFail();
        $order->loadMissing(['customer', 'details'])->get();

        return view('orders.show', [
            'order' => $order
        ]);
    }

    public function update($uuid, Request $request)
    {
        $order = Order::where("uuid", $uuid)->firstOrFail();
        // TODO refactoring

        // Reduce the stock
        $products = OrderDetails::where('order_id', $order->id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                //->update(['stock' => DB::raw('stock-'.$product->quantity)]);
                ->update(['quantity' => DB::raw('quantity-' . $product->quantity)]);
        }

        $order->update([
            'order_status' => OrderStatus::COMPLETE
        ]);

        return redirect()
            ->route('orders.complete')
            ->with('success', 'Order has been completed!');
    }

    public function destroy($uuid)
    {
        $order = Order::where("uuid", $uuid)->firstOrFail();
        $order->delete();
    }

    public function downloadInvoice($uuid)
    {
        $order = Order::with(['customer', 'details'])->where("uuid", $uuid)->firstOrFail();
        // TODO: Need refactor
        //dd($order);

        //$order = Order::with('customer')->where('id', $order_id)->first();
        // $order = Order::
        //     ->where('id', $order)
        //     ->first();

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }
}
