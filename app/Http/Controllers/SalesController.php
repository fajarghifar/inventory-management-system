<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\DTOs\SaleData;
use Illuminate\Http\Request;
use App\Services\SaleService;
use App\Exceptions\SaleException;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSaleRequest;

class SalesController extends Controller
{
    public function index()
    {
        return view('sales.index');
    }

    public function create()
    {
        return view('sales.create');
    }

    public function store(StoreSaleRequest $request, SaleService $saleService)
    {
        try {
            $validated = $request->validated();
            $validated['created_by'] = Auth::id(); // Enforce current user

            $saleData = SaleData::fromRequest($validated);

            $sale = $saleService->createSale($saleData);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Sale created successfully',
                    'data' => $sale
                ], 201);
            }

            return redirect()->route('sales.create')
                ->with('success', 'Sale created successfully.');

        } catch (SaleException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                // Log the confusing error for debugging but return generic message if strict
                // return response()->json(['message' => 'Internal Error'], 500);
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'creator']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        return view('sales.edit', compact('sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        // Implement if needed.
    }

    public function destroy(Request $request, Sale $sale, SaleService $saleService)
    {
        try {
            $reason = $request->input('reason');
            $saleService->cancelSale($sale, $reason);
            return redirect()->route('sales.index')->with('success', 'Sale cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function print(Sale $sale)
    {
        $sale->load(['items.product.unit', 'customer', 'creator']);
        return view('sales.print', compact('sale'));
    }

    public function restore(Sale $sale, SaleService $saleService)
    {
        try {
            $saleService->restoreSale($sale);
            return redirect()->back()->with('success', 'Sale restored to Pending.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, Sale $sale, SaleService $saleService)
    {
        try {
            $paymentData = $request->only(['cash_received', 'change']);

            $saleService->completeSale($sale, $paymentData);

            return redirect()->back()->with('success', 'Sale marked as completed.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
