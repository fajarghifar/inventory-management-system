<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    /**
     * @var SupplierService
     */
    protected SupplierService $supplierService;

    /**
     * SupplierController constructor.
     *
     * @param SupplierService $supplierService
     */
    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSupplierRequest $request
     * @return RedirectResponse
     */
    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        try {
            $this->supplierService->createSupplier($request->validated());

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            // Log is already handled in Service, but we might want context here
            Log::error('Controller: Failed to create supplier', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create supplier. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Supplier $supplier
     * @return View
     */
    public function show(Supplier $supplier): View
    {
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Supplier $supplier
     * @return View
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSupplierRequest $request
     * @param Supplier $supplier
     * @return RedirectResponse
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        try {
            $this->supplierService->updateSupplier($supplier, $request->validated());

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'Supplier updated successfully.');
        } catch (\Exception $e) {
            Log::error('Controller: Failed to update supplier', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update supplier. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Supplier $supplier
     * @return RedirectResponse
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        try {
            $this->supplierService->deleteSupplier($supplier);

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'Supplier deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Controller: Failed to delete supplier', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete supplier. ' . $e->getMessage());
        }
    }
}
