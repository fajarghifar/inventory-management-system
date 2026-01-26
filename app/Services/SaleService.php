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
    /**
     * Create a new sale with items and deduction of stock.
     *
     * @throws SaleException
     */
    public function createSale(SaleData $data): Sale
    {
        return DB::transaction(function () use ($data) {
            try {
                // 1. Optimize: Collect Product IDs & Sort to prevent deadlocks
                $productIds = [];
                foreach ($data->items as $item) {
                    $productIds[] = $item->product_id;
                }
                sort($productIds); // Sort IDs for consistent locking order

                // 2. Fetch all products in one query with locking
                $products = Product::whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // 3. Create Sale Header
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
                    'cash' => $data->cash,
                    'subtotal' => 0,
                    'total_discount' => 0,
                    'total' => 0,
                ]);

                $totalSubtotal = 0;
                $totalDiscount = 0;

                // 4. Process Items (Memory check with pre-loaded products)
                foreach ($data->items as $itemData) {
                    $product = $products->get($itemData->product_id);

                    if (!$product) {
                        throw new Exception("Product ID {$itemData->product_id} not found.");
                    }

                    // Domain Rule: Stock Validation
                    if ($product->quantity < $itemData->quantity) {
                        throw SaleException::insufficientStock(
                            $product->name,
                            $itemData->quantity,
                            $product->quantity
                        );
                    }

                    // Calculation
                    $unitPrice = $itemData->unit_price;
                    $quantity = $itemData->quantity;
                    $discount = $itemData->discount;
                    $finalPrice = $unitPrice - $discount;
                    $subtotal   = $finalPrice * $quantity;

                    // Create Item
                    $sale->items()->create([
                        'product_id'  => $product->id,
                        'quantity'    => $quantity,
                        'cost_price' => $product->purchase_price, // HPP
                        'unit_price'  => $unitPrice,
                        'discount'    => $discount,
                        'final_price' => $finalPrice,
                        'subtotal'    => $subtotal,
                    ]);

                    // Update Stock
                    $product->decrement('quantity', $quantity);

                    $totalSubtotal += $subtotal;
                    $totalDiscount += $discount * $quantity;
                }

                // 5. Update Header Totals
                $sale->update([
                    'subtotal' => $totalSubtotal + $totalDiscount,
                    'total_discount' => $totalDiscount,
                    'total'          => $totalSubtotal,
                ]);

                return $sale;

            } catch (Exception $e) {
                if ($e instanceof SaleException) {
                    throw $e;
                }
                throw SaleException::creationFailed($e->getMessage(), ['data' => $data]);
            }
        });
    }

    /**
     * Cancel a sale and restore stock.
     *
     * @param Sale $sale
     * @return Sale
     * @throws Exception
     */
    public function cancelSale(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            try {
                if ($sale->status === SaleStatus::CANCELLED) {
                    throw SaleException::invalidStatus('cancel', $sale->status->label(), ['id' => $sale->id]);
                }

                // Restore Stock
                // Eager load items to avoid N+1 inside transaction if not already loaded
                $sale->loadMissing('items.product');

                foreach ($sale->items as $item) {
                    if ($item->product) {
                        $item->product->increment('quantity', $item->quantity);
                    }
                }

                $sale->update(['status' => SaleStatus::CANCELLED]);

                return $sale;

            } catch (Exception $e) {
                if ($e instanceof SaleException) {
                    throw $e;
                }
                throw SaleException::cancellationFailed($e->getMessage(), ['id' => $sale->id]);
            }
        });
    }

    /**
     * Generate unique invoice number.
     * Format: INV.YYYYMMDD.0001
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV.' . date('Ymd') . '.';

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
