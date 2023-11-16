<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Order::count();
        $products = Product::count();


        return view('dashboard', [
            'products' => $products,
            'orders' => $orders,
        ]);
    }
}
