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
        $query = trim($request->input('q') ?? $request->input('search') ?? '');

        if (empty($query)) {
            return response()->json([]);
        }

        $products = Product::query()
            ->with(['unit'])
            ->where('quantity', '>', 0) // Only show available products
            ->where(function ($q) use ($query) {
                $q->where('sku', $query)
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->orderByRaw("CASE WHEN LOWER(sku) = LOWER(?) THEN 1 WHEN LOWER(sku) LIKE LOWER(?) THEN 2 ELSE 3 END", [$query, "{$query}%"])
            ->limit(30)
            ->get()
            ->map(function ($product) {
                return [
                    'value' => $product->id,
                    'id' => $product->id,
                    'text' => $product->name,
                    'name' => $product->name,
                    'price' => (float) $product->purchase_price,
                    'selling_price' => (float) $product->selling_price,
                    'sku' => $product->sku,
                    'quantity' => (int) $product->quantity,
                    'unit' => $product->unit ? [
                        'symbol' => $product->unit->symbol,
                        'name' => $product->unit->name
                    ] : null,
                ];
            });

        return response()->json($products);
    }

    public function catalog()
    {
        $products = Product::query()
            ->with(['unit'])
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'value' => $product->id,
                    'id' => $product->id,
                    'text' => $product->name,
                    'name' => $product->name,
                    'price' => (float) $product->purchase_price,
                    'selling_price' => (float) $product->selling_price,
                    'sku' => $product->sku,
                    'quantity' => (int) $product->quantity,
                    'unit' => $product->unit ? [
                        'symbol' => $product->unit->symbol,
                        'name' => $product->unit->name
                    ] : null,
                ];
            });

        return response()->json($products);
    }
}
