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
        $query = $request->input('q') ?? $request->input('search');

        $cacheKey = 'products_search_' . md5($query);

        $products = Cache::remember($cacheKey, 300, function () use ($query) {
            return Product::query()
                ->with(['unit'])
                ->where('quantity', '>', 0) // Only show available products
                ->when($query, function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                })
                ->limit(50)
                ->get()
                ->map(function ($product) {
                    return [
                        'value' => $product->id,
                        'id' => $product->id,
                        'text' => $product->name,
                        'name' => $product->name,
                        'price' => $product->purchase_price,
                        'selling_price' => $product->selling_price,
                        'sku' => $product->sku,
                        'quantity' => $product->quantity,
                        'unit' => $product->unit ? [
                            'symbol' => $product->unit->symbol,
                            'name' => $product->unit->name
                        ] : null,
                    ];
                });
        });

        return response()->json($products);
    }
}
