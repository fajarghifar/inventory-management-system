<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        $cacheKey = 'products_search_' . md5($query);

        $products = Cache::remember($cacheKey, 300, function () use ($query) {
            return Product::query()
                ->with(['unit'])
                ->when($query, function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get()
                ->map(function ($product) {
                    return [
                        'value' => $product->id,
                        'text' => $product->name,
                        'price' => $product->purchase_price,
                        'selling_price' => $product->selling_price,
                        'sku' => $product->sku,
                    ];
                });
        });

        return response()->json($products);
    }
}
