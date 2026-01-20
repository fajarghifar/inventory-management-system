<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\SaleService;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('sales.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sales.create'); // POS View
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id(); // Enforce current user

            $sale = $this->saleService->createSale($data, $data['items']);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Sale created successfully',
                    'data' => $sale
                ], 201);
            }

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale created successfully.');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['details.product', 'customer', 'creator']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        // Typically POS sales are not editable, only voidable.
        // But for generic edit we can return a view.
        return view('sales.edit', compact('sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        // Implement if needed.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        try {
            $this->saleService->cancelSale($sale);
            return redirect()->route('sales.index')->with('success', 'Sale cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
