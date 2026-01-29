<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Services\SaleService;
use App\DTOs\SaleData;
use App\Enums\SaleStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function searchProducts(Request $request)
    {
        $query = $request->get('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $products = Product::query()
            ->with(['unit'])
            ->where('quantity', '>', 0)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->get('query');

        $customers = Customer::query()
            ->when($query, function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create($validated);

        return response()->json($customer);
    }

    public function storeSale(Request $request, SaleService $saleService)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|string',
            'cash_received' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:pending,completed',
        ]);

        try {
            $itemsData = [];
            $total = 0;

            foreach ($validated['items'] as $item) {
                $totalDiscountForLine = $item['discount'] ?? 0;
                $lineTotal = ($item['price'] * $item['quantity']) - $totalDiscountForLine;
                $total += $lineTotal;

                // Calculate effective per-unit discount
                $discountPerUnit = $item['quantity'] > 0 ? ($totalDiscountForLine / $item['quantity']) : 0;

                $itemsData[] = [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount' => $discountPerUnit,
                ];
            }

            // Payment verification
            $cashReceived = $validated['cash_received'] ?? 0;
            $change = 0;

            if ($validated['payment_method'] === 'cash') {
                if ($cashReceived < $total) {
                    return response()->json(['message' => 'Insufficient payment!'], 422);
                }
                $change = max(0, $cashReceived - $total);
            }

            $saleData = SaleData::fromRequest([
                'sale_date' => now()->format('Y-m-d'),
                'payment_method' => $validated['payment_method'],
                'created_by' => Auth::id(),
                'items' => $itemsData,
                'customer_id' => $validated['customer_id'] ?? null,
                'status' => $validated['status'] ?? SaleStatus::COMPLETED->value,
                'notes' => $validated['notes'] ?? '',
                'cash_received' => $cashReceived,
                'change' => $change,
            ]);

            $sale = $saleService->createSale($saleData);

            return response()->json([
                'success' => true,
                'redirect' => route('sales.create'),
                'print_url' => route('sales.print', $sale->id)
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
