<?php

namespace App\Services;

use Exception;
use App\Models\Purchase;
use App\DTOs\PurchaseData;
use App\DTOs\PurchaseItemData;
use App\Enums\PurchaseStatus;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;
use App\Exceptions\PurchaseException;

class PurchaseService
{
    /**
     * Create a new purchase with items.
     *
     * @param PurchaseData $data
     * @param int $userId
     * @return Purchase
     * @throws PurchaseException
     */
    public function createPurchase(PurchaseData $data, int $userId): Purchase
    {
        return DB::transaction(function () use ($data, $userId) {
            try {
                $purchase = Purchase::create([
                    'invoice_number' => $data->invoice_number,
                    'supplier_id' => $data->supplier_id,
                    'purchase_date' => $data->purchase_date,
                    'due_date' => $data->due_date,
                    'status' => $data->status,
                    'notes' => $data->notes,
                    'proof_image' => $data->proof_image,
                    'created_by'     => $userId,
                    'total'          => 0,
                ]);

                $this->syncItems($purchase, $data->items);

                return $purchase;

            } catch (Exception $e) {
                throw PurchaseException::creationFailed($e->getMessage(), ['supplier_id' => $data->supplier_id]);
            }
        });
    }

    /**
     * Update an existing purchase (Only if Draft).
     *
     * @param Purchase $purchase
     * @param PurchaseData $data
     * @return Purchase
     * @throws PurchaseException
     */
    public function updatePurchase(Purchase $purchase, PurchaseData $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            try {
                if (!in_array($purchase->status, [PurchaseStatus::DRAFT, PurchaseStatus::ORDERED])) {
                    throw PurchaseException::invalidStatus('update', $purchase->status->label(), ['id' => $purchase->id]);
                }

                $purchase->update([
                    'invoice_number' => $data->invoice_number,
                    'supplier_id' => $data->supplier_id,
                    'purchase_date' => $data->purchase_date,
                    'due_date' => $data->due_date,
                    'notes' => $data->notes,
                    'proof_image' => $data->proof_image,
                ]);

                // Full sync of items
                $purchase->items()->delete();
                $this->syncItems($purchase, $data->items);

                return $purchase->refresh();

            } catch (Exception $e) {
                if ($e instanceof PurchaseException)
                    throw $e;
                throw PurchaseException::updateFailed($e->getMessage(), ['id' => $purchase->id]);
            }
        });
    }

    /**
     * Delete a purchase.
     *
     * @param Purchase $purchase
     * @return void
     * @throws PurchaseException
     */
    public function deletePurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            try {
                if (!in_array($purchase->status, [PurchaseStatus::DRAFT, PurchaseStatus::CANCELLED])) {
                    throw PurchaseException::deletionFailed(
                        "Cannot delete purchase with status [{$purchase->status->label()}]. Only Draft or Cancelled purchases can be deleted.",
                        ['id' => $purchase->id, 'status' => $purchase->status->value]
                    );
                }

                $purchase->items()->delete();
                $purchase->delete();

            } catch (Exception $e) {
                if ($e instanceof PurchaseException)
                    throw $e;
                throw PurchaseException::deletionFailed($e->getMessage(), ['id' => $purchase->id]);
            }
        });
    }

    /**
     * Mark purchase as Ordered.
     *
     * @throws PurchaseException
     */
    public function markAsOrdered(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status !== PurchaseStatus::DRAFT) {
                throw PurchaseException::invalidStatus('order', $purchase->status->label(), ['id' => $purchase->id]);
            }

            if ($purchase->items()->count() === 0) {
                throw PurchaseException::updateFailed("Cannot order a purchase with no items.", ['id' => $purchase->id]);
            }

            $purchase->update(['status' => PurchaseStatus::ORDERED]);
        });
    }

    /**
     * Mark purchase as Received and update product stock.
     *
     * @throws PurchaseException
     */
    public function markAsReceived(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if (!in_array($purchase->status, [PurchaseStatus::ORDERED, PurchaseStatus::DRAFT])) {
                throw PurchaseException::invalidStatus('receive', $purchase->status->label(), ['id' => $purchase->id]);
            }

            if (empty($purchase->invoice_number)) {
                throw PurchaseException::missingReference('Invoice Number', ['id' => $purchase->id]);
            }

            // Enforce Proof Image
            if (empty($purchase->proof_image)) {
                throw PurchaseException::missingReference('Proof Image', ['id' => $purchase->id]);
            }

            // Update Stock
            foreach ($purchase->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('quantity', $item->quantity);
                    // Update latest purchase price and selling price
                    $updateData = ['purchase_price' => $item->unit_price];
                    if ($item->selling_price) {
                        $updateData['selling_price'] = $item->selling_price;
                    }
                    $product->update($updateData);
                }
            }

            $purchase->update(['status' => PurchaseStatus::RECEIVED]);
        });
    }

    /**
     * Mark purchase as Paid.
     *
     * @throws PurchaseException
     */
    public function markAsPaid(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status === PurchaseStatus::CANCELLED) {
                throw PurchaseException::invalidStatus('pay', $purchase->status->label(), ['id' => $purchase->id]);
            }

            // Strict Validation for Payment
            if (empty($purchase->invoice_number)) {
                throw PurchaseException::missingReference('Invoice Number', ['id' => $purchase->id]);
            }

            if (empty($purchase->proof_image)) {
                throw PurchaseException::missingReference('Proof Image', ['id' => $purchase->id]);
            }

            $purchase->update(['status' => PurchaseStatus::PAID]);
        });
    }

    /**
     * Cancel purchase.
     *
     * @throws PurchaseException
     */
    public function cancelPurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status === PurchaseStatus::RECEIVED || $purchase->status === PurchaseStatus::PAID) {
                throw PurchaseException::invalidStatus('cancel', $purchase->status->label(), ['id' => $purchase->id]);
            }

            $purchase->update(['status' => PurchaseStatus::CANCELLED]);
        });
    }

    /**
     * Restore cancelled purchase to draft.
     *
     * @throws PurchaseException
     */
    public function restoreToDraft(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status !== PurchaseStatus::CANCELLED) {
                throw PurchaseException::invalidStatus('restore', $purchase->status->label(), ['id' => $purchase->id]);
            }

            $purchase->update(['status' => PurchaseStatus::DRAFT]);
        });
    }

    /**
     * Internal helper to sync purchase items and calculate total.
     *
     * @param Purchase $purchase
     * @param array<PurchaseItemData> $items
     */
    private function syncItems(Purchase $purchase, array $items): void
    {
        $total = 0;

        foreach ($items as $itemData) {
            $subtotal = $itemData->quantity * $itemData->unit_price;

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $itemData->product_id,
                'quantity' => $itemData->quantity,
                'unit_price' => $itemData->unit_price,
                'subtotal'    => $subtotal,
                'selling_price' => $itemData->selling_price,
            ]);

            $total += $subtotal;
        }

        $purchase->update(['total' => $total]);
    }
}
