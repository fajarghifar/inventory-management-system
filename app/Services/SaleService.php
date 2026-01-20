<?php

namespace App\Services;

use App\Enums\SaleStatus;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class SaleService
{
    /**
     * Create a new sale transaction.
     *
     * @param array $data Sale header data
     * @param array $items Array of items ['product_id', 'quantity', 'discount', 'unit_price']
     * @return Sale
     * @throws Exception
     */
    public function createSale(array $data, array $items): Sale
    {
        return DB::transaction(function () use ($data, $items) {
            try {
                // 1. Create Header
                $sale = Sale::create([
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'customer_id'    => $data['customer_id'] ?? null,
                    'created_by'     => $data['created_by'],
                    'sale_date'      => $data['sale_date'],
                    'status'         => $data['status'] ?? SaleStatus::COMPLETED,
                    'payment_method' => $data['payment_method'],
                    'notes'          => $data['notes'] ?? null,
                    'cash_received'  => $data['cash_received'] ?? 0,
                    'change'         => $data['change'] ?? 0,
                    'cash'           => $data['cash'] ?? 0, // Explicit cash amount if split payment
                    'subtotal'       => 0, // Recalculated below
                    'total_discount' => 0, // Recalculated below
                    'total'          => 0, // Recalculated below
                ]);

                $totalSubtotal = 0;
                $totalDiscount = 0;

                // 2. Process Items
                foreach ($items as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if (!$product) {
                        throw new Exception("Product ID {$item['product_id']} not found.");
                    }

                    // Stock Validation
                    if ($product->quantity < $item['quantity']) {
                        throw new Exception("Insufficient stock for product {$product->name}. Requested: {$item['quantity']}, Available: {$product->quantity}");
                    }

                    // Calculation (Backend Trust)
                    $unitPrice  = $item['unit_price']; // From Input (POS override) or Product? Usually POS sends price.
                    $quantity   = $item['quantity'];
                    $discount   = $item['discount'] ?? 0;
                    $finalPrice = $unitPrice - $discount;
                    $subtotal   = $finalPrice * $quantity;

                    // Create Detail
                    $sale->details()->create([
                        'product_id'  => $product->id,
                        'quantity'    => $quantity,
                        'cost_price'  => $product->purchase_price, // HPP Snapshot
                        'unit_price'  => $unitPrice,
                        'discount'    => $discount,
                        'final_price' => $finalPrice,
                        'subtotal'    => $subtotal,
                    ]);

                    // Update Stock
                    $product->decrement('quantity', $quantity);

                    $totalSubtotal += $subtotal;
                    $totalDiscount += $discount * $quantity; // Assuming discount is per unit
                }

                // 3. Update Header Totals
                $sale->update([
                    'subtotal'       => $totalSubtotal + $totalDiscount, // Gross subtotal
                    'total_discount' => $totalDiscount,
                    'total'          => $totalSubtotal,
                ]);

                return $sale->load(['details', 'customer', 'creator']);

            } catch (Exception $e) {
                Log::error('Failed to create sale: ' . $e->getMessage());
                throw $e; // Re-throw to trigger rollback
            }
        });
    }

    /**
     * Cancel a sale (void).
     *
     * @param Sale $sale
     * @return Sale
     * @throws Exception
     */
    public function cancelSale(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            if ($sale->status === SaleStatus::CANCELLED) {
                throw new Exception("Sale is already cancelled.");
            }

            // Restore Stock
            foreach ($sale->details as $detail) {
                $detail->product->increment('quantity', $detail->quantity);
            }

            $sale->update(['status' => SaleStatus::CANCELLED]);

            return $sale;
        });
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV/' . date('Ymd') . '/';
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
