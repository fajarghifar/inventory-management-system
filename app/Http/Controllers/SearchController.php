<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;

class SearchController extends Controller
{
    public function searchProducts(Request $request)
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

    public function searchSuppliers(Request $request)
    {
        $query = $request->get('q');

        $suppliers = Supplier::query()
            ->when($query, function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function($supplier) {
                return [
                    'value' => $supplier->id,
                    'text' => $supplier->name . ($supplier->phone ? ' | ' . $supplier->phone : ''),
                ];
            });

        return response()->json($suppliers);
    }
}
