<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\DTOs\PurchaseData;
use Illuminate\Http\Request;
use App\Enums\PurchaseStatus;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\PurchaseException;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;

class PurchaseController extends Controller
{
    protected PurchaseService $service;

    public function __construct(PurchaseService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('purchases.index');
    }

    public function create()
    {
        return view('purchases.create', [
            'purchase' => new Purchase(),
            'statuses' => PurchaseStatus::cases(),
        ]);
    }

    public function store(StorePurchaseRequest $request)
    {
        return $this->runAction(function () use ($request) {
            $data = $request->validated();
            $data['proof_image'] = $this->storeProofImage($request);
            $data['status'] = PurchaseStatus::DRAFT->value; // Force Draft on Create

            $purchaseData = PurchaseData::fromArray($data);

            $purchase = $this->service->createPurchase($purchaseData, Auth::id());

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase created successfully.');
        }, 'Error creating purchase: ', withInput: true);
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'creator', 'items.product.unit']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if (!$purchase->status->isEditable()) {
            abort(403, 'Only draft or ordered purchases can be edited.');
        }

        // Load relationships needed for the form
        $purchase->load('items.product', 'supplier');

        return view('purchases.edit', [
            'purchase' => $purchase,
            'statuses' => PurchaseStatus::cases(),
        ]);
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        return $this->runAction(function () use ($request, $purchase) {
            $data = $request->validated();
            $data['proof_image'] = $this->storeProofImage($request, $purchase->proof_image);
            $data['status'] = $purchase->status->value; // Preserve existing status

            $purchaseData = PurchaseData::fromArray($data);

            $this->service->updatePurchase($purchase, $purchaseData);

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase updated successfully.');
        }, 'Error updating purchase: ', withInput: true);
    }

    public function destroy(Purchase $purchase)
    {
        return $this->runAction(function () use ($purchase) {
            $this->service->deletePurchase($purchase);
            return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
        }, 'Error deleting purchase: ');
    }

    public function markOrdered(Purchase $purchase)
    {
        return $this->runAction(function () use ($purchase) {
            $this->service->markAsOrdered($purchase);
            return back()->with('success', 'Purchase marked as ordered.');
        }, 'Error marking as ordered: ');
    }

    public function markReceived(Request $request, Purchase $purchase)
    {
        $rules = [];

        // Only validate invoice_number if it's not already set on the purchase
        if (empty($purchase->invoice_number)) {
            $rules['invoice_number'] = 'required|string|max:255';
        }

        if (empty($purchase->proof_image)) {
            $rules['proof_image'] = 'required|image|max:2048'; // 2MB Max
        }

        // Tetap DI LUAR runAction agar ValidationException tidak tertangkap catch(\Exception)
        $request->validate($rules);

        return $this->runAction(function () use ($request, $purchase) {
            $updateData = [];

            if ($request->filled('invoice_number')) {
                $updateData['invoice_number'] = $request->invoice_number;
            }

            $proofPath = $this->storeProofImage($request);
            if ($proofPath !== null) {
                $updateData['proof_image'] = $proofPath;
            }

            if (!empty($updateData)) {
                $purchase->update($updateData);
            }

            $this->service->markAsReceived($purchase);

            return back()->with('success', 'Purchase received and stock updated.');
        }, 'Error receiving purchase: ');
    }

    public function cancel(Purchase $purchase)
    {
        return $this->runAction(function () use ($purchase) {
            $this->service->cancelPurchase($purchase);
            return back()->with('success', 'Purchase order cancelled.');
        }, 'Error cancelling purchase: ');
    }

    public function markPaid(Purchase $purchase)
    {
        return $this->runAction(function () use ($purchase) {
            $this->service->markAsPaid($purchase);
            return back()->with('success', 'Purchase marked as paid.');
        }, 'Error marking as paid: ');
    }

    public function restoreToDraft(Purchase $purchase)
    {
        return $this->runAction(function () use ($purchase) {
            $this->service->restoreToDraft($purchase);
            return back()->with('success', 'Purchase restored to draft.');
        }, 'Error restoring purchase: ');
    }

    /**
     * Jalankan aksi; ubah exception menjadi redirect kembali dengan pesan error.
     * PurchaseException -> pesannya ditampilkan apa adanya.
     * Exception lain    -> diberi awalan $errorPrefix.
     */
    private function runAction(callable $action, string $errorPrefix, bool $withInput = false): RedirectResponse
    {
        try {
            return $action();
        } catch (PurchaseException $e) {
            return $this->backWithError($e->getMessage(), $withInput);
        } catch (\Exception $e) {
            return $this->backWithError($errorPrefix . $e->getMessage(), $withInput);
        }
    }

    private function backWithError(string $message, bool $withInput): RedirectResponse
    {
        $response = back()->with('error', $message);

        return $withInput ? $response->withInput() : $response;
    }

    /**
     * Simpan bukti pembayaran bila ada file baru; jika tidak, kembalikan path lama.
     */
    private function storeProofImage(Request $request, ?string $currentPath = null): ?string
    {
        if (!$request->hasFile('proof_image')) {
            return $currentPath;
        }

        return $request->file('proof_image')->store('proofs', 'public');
    }
}
