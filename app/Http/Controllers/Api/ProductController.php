<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');

        $products = Product::query()
            ->with(['unit']) // Eager load unit if needed for display
            ->when($query, function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function($product) {
                return [
                    'value' => $product->id,
                    'text' => $product->name, // Standard text for TomSelect
                    'price' => $product->purchase_price, // Extra data for auto-fill
                    'selling_price' => $product->selling_price,
                    'sku' => $product->sku,
                ];
            });

        return response()->json($products);
    }
}
