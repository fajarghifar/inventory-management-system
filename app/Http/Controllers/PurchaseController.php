<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Purchase;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Enums\PurchaseStatus;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function index(): View
    {
        return view('purchases.index');
    }

    public function create(): View
    {
        return view('purchases.create');
    }

    public function edit(Purchase $purchase): View
    {
        if (!in_array($purchase->status, [PurchaseStatus::DRAFT, PurchaseStatus::ORDERED])) {
            abort(403, 'Only draft or ordered purchases can be edited.');
        }
        return view('purchases.edit', compact('purchase'));
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'items.product', 'creator']);
        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        try {
            $this->purchaseService->deletePurchase($purchase);
            return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markOrdered(Purchase $purchase): RedirectResponse
    {
        try {
            $this->purchaseService->markAsOrdered($purchase);
            return back()->with('success', 'Purchase marked as Ordered.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markReceived(Request $request, Purchase $purchase): RedirectResponse
    {
        $request->validate([
            'invoice_number' => $purchase->invoice_number ? 'nullable|string|max:255' : 'required|string|max:255',
            'proof_image' => $purchase->proof_image ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        try {
            if ($request->hasFile('proof_image')) {
                $path = $request->file('proof_image')->store('purchase-proofs', 'public');
                $purchase->update(['proof_image' => $path]);
            }

            if ($request->filled('invoice_number')) {
                $purchase->update(['invoice_number' => $request->invoice_number]);
            }

            $this->purchaseService->markAsReceived($purchase);
            return back()->with('success', 'Purchase received and stock updated.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markPaid(Purchase $purchase): RedirectResponse
    {
        try {
            $this->purchaseService->markAsPaid($purchase);
            return back()->with('success', 'Purchase marked as Paid.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Purchase $purchase): RedirectResponse
    {
        try {
            $this->purchaseService->cancelPurchase($purchase);
            return back()->with('success', 'Purchase cancelled.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function restoreToDraft(Purchase $purchase): RedirectResponse
    {
        try {
            $this->purchaseService->restoreToDraft($purchase);
            return back()->with('success', 'Purchase restored to draft.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
