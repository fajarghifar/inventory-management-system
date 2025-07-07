<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Category;
use App\Models\Quotation;
use App\Enums\OrderStatus;

class DashboardController extends Controller
{
    public function index()
    {
        // Count all orders
        $orders = Order::count();

        // Count only completed orders
        $completedOrders = Order::where('order_status', OrderStatus::COMPLETE)->count();

        // Count all products
        $products = Product::count();

        // Count all purchases
        $purchases = Purchase::count();

        // Count today’s purchases
        $todayPurchases = Purchase::whereDate('date', today())->count();

        // Count categories
        $categories = Category::count();

        // Count all quotations
        $quotations = Quotation::count();

        // Count today’s quotations
        $todayQuotations = Quotation::whereDate('date', today())->count();

        // Return the data to the dashboard view
        return view('dashboard', [
            'orders' => $orders,
            'completedOrders' => $completedOrders,
            'products' => $products,
            'purchases' => $purchases,
            'todayPurchases' => $todayPurchases,
            'categories' => $categories,
            'quotations' => $quotations,
            'todayQuotations' => $todayQuotations,
        ]);
    }
}
