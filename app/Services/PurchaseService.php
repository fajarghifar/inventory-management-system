<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\Purchase;
use App\DTOs\PurchaseData;
use App\Models\PurchaseItem;
use App\Enums\PurchaseStatus;
use App\DTOs\PurchaseItemData;
use Illuminate\Support\Facades\DB;
use App\Exceptions\PurchaseException;

class PurchaseService
{
    public function __construct(
        protected FinanceTransactionService $financeService
    ) {
    }

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

                $this->financeService->voidTransaction($purchase);

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
                // Lock the product row for update to prevent race conditions
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                if ($product) {
                    $product->increment('quantity', $item->quantity);

                    // Update latest purchase price and selling price
                    $updateData = ['purchase_price' => $item->unit_price];
                    $priceChangeLog = "";
                    $hasPriceChange = false;

                    // Check for Purchase Price Change
                    if ((float) $product->purchase_price !== (float) $item->unit_price) {
                        $hasPriceChange = true;
                        $oldBuy = number_format($product->purchase_price ?? 0, 0, ',', '.');
                        $newBuy = number_format($item->unit_price, 0, ',', '.');
                        $priceChangeLog .= "\n- Buying Price: Rp {$oldBuy} -> Rp {$newBuy}";
                    }

                    // Check for Selling Price Change
                    if ($item->selling_price) {
                        $updateData['selling_price'] = $item->selling_price;
                        if ((float) $product->selling_price !== (float) $item->selling_price) {
                            $hasPriceChange = true;
                            $oldSell = number_format($product->selling_price ?? 0, 0, ',', '.');
                            $newSell = number_format($item->selling_price, 0, ',', '.');
                            $priceChangeLog .= "\n- Selling Price: Rp {$oldSell} -> Rp {$newSell}";
                        }
                    }

                    // Append to Notes if prices changed
                    if ($hasPriceChange) {
                        $timestamp = now()->format('Y-m-d H:i');
                        $ref = $purchase->invoice_number ? "Invoice #{$purchase->invoice_number}" : "Purchase #{$purchase->id}";
                        $logHeader = "\n\n[System Log - {$timestamp}] Price update via {$ref}:";
                        $updateData['notes'] = TRIM(($product->notes ?? '') . $logHeader . $priceChangeLog);
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

            $this->financeService->recordExpenseFromPurchase($purchase);
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

            $this->financeService->voidTransaction($purchase);
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
