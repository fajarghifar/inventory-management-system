<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\Purchase;
use App\DTOs\PurchaseData;
use App\Models\PurchaseItem;
use App\Enums\PurchaseStatus;
use Illuminate\Support\Facades\DB;
use App\Exceptions\PurchaseException;

class PurchaseService
{
    public function __construct(
        protected FinanceTransactionService $financeService
    ) {
    }

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

    public function updatePurchase(Purchase $purchase, PurchaseData $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            try {
                if (!$purchase->status->isEditable()) {
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

    public function deletePurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            try {
                if (!$purchase->status->isDeletable()) {
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

    public function markAsOrdered(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if (!$purchase->status->canBeOrdered()) {
                throw PurchaseException::invalidStatus('order', $purchase->status->label(), ['id' => $purchase->id]);
            }

            if ($purchase->items()->count() === 0) {
                throw PurchaseException::updateFailed("Cannot order a purchase with no items.", ['id' => $purchase->id]);
            }

            $purchase->update(['status' => PurchaseStatus::ORDERED]);
        });
    }

    public function markAsReceived(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if (!$purchase->status->canBeReceived()) {
                throw PurchaseException::invalidStatus('receive', $purchase->status->label(), ['id' => $purchase->id]);
            }

            $this->ensureHasRequiredReferences($purchase);

            foreach ($purchase->items as $item) {
                // Lock the product row for update to prevent race conditions
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                if ($product) {
                    $this->receiveItemIntoStock($product, $item, $purchase);
                }
            }

            $purchase->update(['status' => PurchaseStatus::RECEIVED]);
        });
    }

    private function receiveItemIntoStock(Product $product, PurchaseItem $item, Purchase $purchase): void
    {
        $product->increment('quantity', $item->quantity);

        $updateData = ['purchase_price' => $item->unit_price];

        if ($item->selling_price) {
            $updateData['selling_price'] = $item->selling_price;
        }

        // Dihitung SEBELUM $product->update() agar harga lama yang tercatat
        $priceChangeLog = $this->buildPriceChangeLog($product, $item);

        if ($priceChangeLog !== '') {
            $updateData['notes'] = $this->appendPriceLogToNotes($product->notes, $purchase, $priceChangeLog);
        }

        $product->update($updateData);
    }

    private function buildPriceChangeLog(Product $product, PurchaseItem $item): string
    {
        $log = '';

        if ((float) $product->purchase_price !== (float) $item->unit_price) {
            $log .= $this->formatPriceChange('Buying Price', $product->purchase_price, $item->unit_price);
        }

        if ($item->selling_price && (float) $product->selling_price !== (float) $item->selling_price) {
            $log .= $this->formatPriceChange('Selling Price', $product->selling_price, $item->selling_price);
        }

        return $log;
    }

    private function formatPriceChange(string $label, $oldPrice, $newPrice): string
    {
        return "\n- {$label}: " . format_money($oldPrice ?? 0) . ' -> ' . format_money($newPrice);
    }

    /**
     * invoice_number dijamin terisi oleh ensureHasRequiredReferences(),
     * sehingga cabang lama "Purchase #id" adalah dead code dan dihapus.
     */
    private function appendPriceLogToNotes(?string $notes, Purchase $purchase, string $priceChangeLog): string
    {
        $timestamp = now()->format('Y-m-d H:i');
        $header = "\n\n[System Log - {$timestamp}] Price update via Invoice #{$purchase->invoice_number}:";

        return trim(($notes ?? '') . $header . $priceChangeLog);
    }

    public function markAsPaid(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if (!$purchase->status->canBePaid()) {
                throw PurchaseException::invalidStatus('pay', $purchase->status->label(), ['id' => $purchase->id]);
            }

            $this->ensureHasRequiredReferences($purchase);

            $purchase->update(['status' => PurchaseStatus::PAID]);

            $this->financeService->recordExpenseFromPurchase($purchase);
        });
    }

    public function cancelPurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if (!$purchase->status->canBeCancelled()) {
                throw PurchaseException::invalidStatus('cancel', $purchase->status->label(), ['id' => $purchase->id]);
            }

            $purchase->update(['status' => PurchaseStatus::CANCELLED]);

            $this->financeService->voidTransaction($purchase);
        });
    }

    public function restoreToDraft(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if (!$purchase->status->canBeRestored()) {
                throw PurchaseException::invalidStatus('restore', $purchase->status->label(), ['id' => $purchase->id]);
            }

            $purchase->update(['status' => PurchaseStatus::DRAFT]);
        });
    }

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

    /**
     * Pembelian yang diterima atau dibayar wajib memiliki nomor invoice dan bukti gambar.
     */
    private function ensureHasRequiredReferences(Purchase $purchase): void
    {
        if (empty($purchase->invoice_number)) {
            throw PurchaseException::missingReference('Invoice Number', ['id' => $purchase->id]);
        }

        if (empty($purchase->proof_image)) {
            throw PurchaseException::missingReference('Proof Image', ['id' => $purchase->id]);
        }
    }
}
