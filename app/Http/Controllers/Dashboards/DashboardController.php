<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Order::count();
        $products = Product::count();

        $purchases = Purchase::count();
        $todayPurchases = Purchase::query()
            //->where('purchase_status', '=', 1)
            ->where('date', today()->format('Y-m-d'))->get()
            ->count();
        $categories = Category::count();
        $quotations = Quotation::count();

        return view('dashboard', [
            'products' => $products,
            'orders' => $orders,
            'purchases' => $purchases,
            'todayPurchases' => $todayPurchases,
            'categories' => $categories,
            'quotations' => $quotations
        ]);
    }
}
