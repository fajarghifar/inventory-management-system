<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\Purchase;
use App\Enums\PurchaseStatus;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseService
{
    /**
     * Create a new purchase with details.
     *
     * @param array $data Purchase main data (supplier_id, dates, notes, etc.)
     * @param array $details Array of items (product_id, quantity, unit_price)
     * @param int $userId ID of the user creating the purchase
     * @return Purchase
     * @throws Exception
     */
    public function createPurchase(array $data, array $details, int $userId): Purchase
    {
        return DB::transaction(function () use ($data, $details, $userId) {
            try {
                Log::info('Attempting to create purchase', ['supplier_id' => $data['supplier_id']]);

                $purchase = Purchase::create([
                    'invoice_number' => $data['invoice_number'] ?? null,
                    'supplier_id'    => $data['supplier_id'],
                    'purchase_date'  => $data['purchase_date'],
                    'due_date'       => $data['due_date'] ?? null,
                    'status'         => $data['status'] ?? PurchaseStatus::DRAFT,
                    'notes'          => $data['notes'] ?? null,
                    'created_by'     => $userId,
                    'total'          => 0,
                ]);

                $this->syncDetails($purchase, $details);

                Log::info('Purchase created successfully', ['id' => $purchase->id]);

                return $purchase;

            } catch (Exception $e) {
                Log::error('Failed to create purchase: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                throw $e;
            }
        });
    }

    /**
     * Update an existing purchase (Only if Draft).
     *
     * @param Purchase $purchase
     * @param array $data
     * @param array|null $details If null, details are not updated.
     * @return Purchase
     * @throws Exception
     */
    public function updatePurchase(Purchase $purchase, array $data, ?array $details = null): Purchase
    {
        return DB::transaction(function () use ($purchase, $data, $details) {
            try {
                if (!in_array($purchase->status, [PurchaseStatus::DRAFT, PurchaseStatus::ORDERED])) {
                    throw new Exception("Only Draft or Ordered purchases can be updated. Current status: {$purchase->status->label()}");
                }

                $purchase->update([
                    'invoice_number' => $data['invoice_number'] ?? $purchase->invoice_number,
                    'supplier_id'    => $data['supplier_id'] ?? $purchase->supplier_id,
                    'purchase_date'  => $data['purchase_date'] ?? $purchase->purchase_date,
                    'due_date'       => $data['due_date'] ?? $purchase->due_date,
                    'notes'          => $data['notes'] ?? $purchase->notes,
                ]);

                if (!is_null($details)) {
                    // Remove old details and re-create (simplest strategy for strict consistency)
                    $purchase->details()->delete();
                    $this->syncDetails($purchase, $details);
                }

                Log::info('Purchase updated successfully', ['id' => $purchase->id]);

                return $purchase->refresh();

            } catch (Exception $e) {
                Log::error('Failed to update purchase: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Delete a purchase.
     *
     * @param Purchase $purchase
     * @return void
     * @throws Exception
     */
    public function deletePurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if (!in_array($purchase->status, [PurchaseStatus::DRAFT, PurchaseStatus::CANCELLED])) {
                throw new Exception("Cannot delete purchase with status [{$purchase->status->label()}]. Only Draft or Cancelled purchases can be deleted.");
            }

            // Details are deleted via cascade in DB usually, but strictly handling here is safer
            $purchase->details()->delete();
            $purchase->delete();

            Log::info('Purchase deleted successfully', ['id' => $purchase->id]);
        });
    }

    /**
     * Mark purchase as Ordered.
     */
    public function markAsOrdered(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status !== PurchaseStatus::DRAFT) {
                throw new Exception("Purchase in status {$purchase->status->label()} cannot be marked as ordered.");
            }

            if ($purchase->details()->count() === 0) {
                throw new Exception("Cannot order a purchase with no items.");
            }

            $purchase->update(['status' => PurchaseStatus::ORDERED]);
            Log::info('Purchase marked as Ordered', ['id' => $purchase->id]);
        });
    }

    /**
     * Mark purchase as Received and update product stock.
     */
    public function markAsReceived(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if (!in_array($purchase->status, [PurchaseStatus::ORDERED, PurchaseStatus::DRAFT])) {
                throw new Exception("Purchase in status {$purchase->status->label()} cannot be received.");
            }

            if (empty($purchase->invoice_number)) {
                throw new Exception("Invoice number is required to receive items.");
            }

            if (empty($purchase->proof_image)) {
                throw new Exception("Proof image is required.");
            }

            // Update Stock
            foreach ($purchase->details as $detail) {
                $product = $detail->product;
                if ($product) {
                    $product->increment('quantity', $detail->quantity);
                    $product->update(['purchase_price' => $detail->unit_price]);
                }
            }

            $purchase->update(['status' => PurchaseStatus::RECEIVED]);
            Log::info('Purchase marked as Received', ['id' => $purchase->id]);
        });
    }

    /**
     * Mark purchase as Paid.
     */
    public function markAsPaid(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status === PurchaseStatus::CANCELLED) {
                throw new Exception("Cancelled purchase cannot be paid.");
            }

            $purchase->update(['status' => PurchaseStatus::PAID]);
            Log::info('Purchase marked as Paid', ['id' => $purchase->id]);
        });
    }

    /**
     * Cancel purchase.
     */
    public function cancelPurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status === PurchaseStatus::RECEIVED || $purchase->status === PurchaseStatus::PAID) {
                throw new Exception("Cannot cancel purchase that is already received or paid.");
            }

            $purchase->update(['status' => PurchaseStatus::CANCELLED]);
            Log::info('Purchase cancelled', ['id' => $purchase->id]);
        });
    }

    /**
     * Restore cancelled purchase to draft.
     */
    public function restoreToDraft(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status !== PurchaseStatus::CANCELLED) {
                throw new Exception("Only cancelled purchases can be restored to draft.");
            }

            $purchase->update(['status' => PurchaseStatus::DRAFT]);
            Log::info('Purchase restored to draft', ['id' => $purchase->id]);
        });
    }

    /**
     * Internal helper to sync purchase details and calculate total.
     */
    private function syncDetails(Purchase $purchase, array $details): void
    {
        $total = 0;

        foreach ($details as $item) {
            $qty = $item['quantity'];
            $price = $item['unit_price'];
            $subtotal = $qty * $price;

            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id'  => $item['product_id'],
                'quantity'    => $qty,
                'unit_price'  => $price,
                'subtotal'    => $subtotal,
            ]);

            $total += $subtotal;
        }

        $purchase->update(['total' => $total]);
    }
}
