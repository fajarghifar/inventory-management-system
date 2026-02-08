<?php

namespace App\Services;

use Exception;
use App\Models\Sale;
use App\Models\Product;
use App\DTOs\SaleData;
use App\Enums\SaleStatus;
use App\Exceptions\SaleException;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected FinanceTransactionService $financeService
    ) {
    }

    /**
     * Create a new sale with items and deduction of stock.
     */
    public function createSale(SaleData $data): Sale
    {
        return DB::transaction(function () use ($data) {
            try {
                // Collect and sort products for locking
                $productIds = collect($data->items)->pluck('product_id')->sort()->values()->all();

                $products = Product::whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $sale = Sale::create([
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'customer_id' => $data->customer_id,
                    'created_by' => $data->created_by,
                    'sale_date' => $data->sale_date,
                    'status' => $data->status,
                    'payment_method' => $data->payment_method,
                    'notes' => $data->notes,
                    'cash_received' => $data->cash_received,
                    'change' => $data->change,
                    'subtotal' => 0,
                    'total_discount' => 0,
                    'total' => 0,
                ]);

                $totalSubtotal = 0;
                $totalDiscount = 0;

                foreach ($data->items as $itemData) {
                    $product = $products->get($itemData->product_id);

                    if (!$product) {
                        throw new Exception("Product ID {$itemData->product_id} not found.");
                    }

                    // Stock validation
                    if ($product->quantity < $itemData->quantity) {
                        throw SaleException::insufficientStock(
                            $product->name,
                            $itemData->quantity,
                            $product->quantity
                        );
                    }
                    $product->decrement('quantity', $itemData->quantity);

                    // Financials
                    $unitPrice = $product->selling_price;
                    $quantity = $itemData->quantity;
                    $discount = $itemData->discount;
                    $finalPrice = $unitPrice - $discount;
                    $subtotal   = $finalPrice * $quantity;

                    $sale->items()->create([
                        'product_id'  => $product->id,
                        'quantity'    => $quantity,
                        'cost_price' => $product->purchase_price,
                        'unit_price'  => $unitPrice,
                        'discount'    => $discount,
                        'final_price' => $finalPrice,
                        'subtotal'    => $subtotal,
                    ]);

                    $totalSubtotal += $subtotal;
                    $totalDiscount += $discount * $quantity;
                }

                $sale->update([
                    'subtotal' => $totalSubtotal + $totalDiscount,
                    'total_discount' => $totalDiscount,
                    'total'          => $totalSubtotal,
                ]);

                // Sync Finance if Completed
                if ($sale->status === SaleStatus::COMPLETED) {
                    $this->financeService->recordIncomeFromSale($sale);
                }

                return $sale;

            } catch (Exception $e) {
                if ($e instanceof SaleException)
                    throw $e;
                throw SaleException::creationFailed($e->getMessage(), ['data' => $data]);
            }
        });
    }

    /**
     * Cancel a sale and restore stock.
     */
    public function cancelSale(Sale $sale, ?string $reason = null): Sale
    {
        return DB::transaction(function () use ($sale, $reason) {
            try {
                if ($sale->status === SaleStatus::CANCELLED) {
                    throw SaleException::invalidStatus('cancel', $sale->status->label(), ['id' => $sale->id]);
                }

                // Restore stock for completed or pending sales
                if (in_array($sale->status, [SaleStatus::COMPLETED, SaleStatus::PENDING])) {
                    $sale->loadMissing('items.product');

                    foreach ($sale->items as $item) {
                        if ($item->product) {
                            $item->product->increment('quantity', $item->quantity);
                        }
                    }
                }

                $updateData = ['status' => SaleStatus::CANCELLED];

                if ($reason) {
                    $updateData['notes'] = ($sale->notes ? $sale->notes . "\n" : '') . "[Cancelled]: " . $reason;
                }

                $sale->update($updateData);

                // Void Finance
                $this->financeService->voidTransaction($sale);

                return $sale;

            } catch (Exception $e) {
                if ($e instanceof SaleException)
                    throw $e;
                throw SaleException::cancellationFailed($e->getMessage(), ['id' => $sale->id]);
            }
        });
    }

    /**
     * Mark a pending sale as completed.
     */
    public function completeSale(Sale $sale, array $paymentData = []): Sale
    {
        return DB::transaction(function () use ($sale, $paymentData) {
            if ($sale->status !== SaleStatus::PENDING) {
                throw SaleException::invalidStatus('complete', $sale->status->label(), ['id' => $sale->id]);
            }

            $updateData = ['status' => SaleStatus::COMPLETED];

            if (!empty($paymentData)) {
                $updateData['cash_received'] = $paymentData['cash_received'] ?? $sale->cash_received;
                $updateData['change'] = $paymentData['change'] ?? $sale->change;
            }

            $sale->update($updateData);

            // Sync Finance
            $this->financeService->recordIncomeFromSale($sale);

            return $sale;
        });
    }

    /**
     * Restore a cancelled sale to pending (must reserve stock again).
     */
    public function restoreSale(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            if ($sale->status !== SaleStatus::CANCELLED) {
                throw SaleException::invalidStatus('restore', $sale->status->label(), ['id' => $sale->id]);
            }

            // Must re-deduct stock
            $sale->loadMissing('items.product');

            foreach ($sale->items as $item) {
                $product = $item->product()->lockForUpdate()->find($item->product_id);

                if (!$product) {
                    throw new Exception("Product ID {$item->product_id} not found.");
                }

                if ($product->quantity < $item->quantity) {
                    throw SaleException::insufficientStock(
                        $product->name,
                        $item->quantity,
                        $product->quantity
                    );
                }

                $product->decrement('quantity', $item->quantity);
            }

            // Restore to PENDING
            $sale->update(['status' => SaleStatus::PENDING]);

            // No Finance Sync needed as it goes to PENDING

            return $sale;
        });
    }

    /**
     * Permanently delete a cancelled sale.
     *
     * @param Sale $sale
     * @return void
     * @throws Exception
     */
    public function deleteSale(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            if ($sale->status !== SaleStatus::CANCELLED) {
                throw SaleException::invalidStatus('delete', $sale->status->label(), ['id' => $sale->id]);
            }

            // Void Finance (Just in case)
            $this->financeService->voidTransaction($sale);

            // Manually delete items first due to restrictOnDelete constraint
            $sale->items()->delete();
            $sale->delete();
        });
    }

    /**
     * Generate unique invoice number.
     * Format: INV.YYMMDD.0001
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV.' . date('ymd') . '.';

        $latest = Sale::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$latest) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($latest->invoice_number, -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
