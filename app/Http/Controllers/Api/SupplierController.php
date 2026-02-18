<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function search(Request $request)
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
