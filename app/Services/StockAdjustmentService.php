<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Enums\StockMovementType;
use App\Enums\StockAdjustmentType;
use App\Enums\StockAdjustmentReason;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentService
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    /**
     * Create an authorized stock adjustment.
     */
    public function createAdjustment(array $data, ?int $userId = null): StockAdjustment
    {
        return DB::transaction(function () use ($data, $userId) {
            $userId = $userId ?? Auth::id();
            $productId = $data['product_id'];
            $quantity = (int) $data['quantity'];
            $type = $data['type'] instanceof StockAdjustmentType ? $data['type'] : StockAdjustmentType::from($data['type']);
            $reason = $data['reason'] instanceof StockAdjustmentReason ? $data['reason'] : StockAdjustmentReason::from($data['reason']);
            $notes = $data['notes'] ?? null;

            if ($quantity <= 0) {
                throw new Exception('Adjustment quantity must be greater than zero.');
            }

            $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            $costPrice = (float) ($product->purchase_price ?? 0);
            $adjustmentNumber = $this->generateAdjustmentNumber();

            $adjustment = StockAdjustment::create([
                'adjustment_number' => $adjustmentNumber,
                'product_id' => $product->id,
                'type' => $type,
                'reason' => $reason,
                'quantity' => $quantity,
                'cost_price_at_time' => $costPrice,
                'user_id' => $userId,
                'notes' => $notes,
            ]);

            // Determine stock movement direction
            $signedQuantity = ($type === StockAdjustmentType::ADDITION) ? $quantity : -$quantity;
            $movementType = ($type === StockAdjustmentType::ADDITION)
                ? StockMovementType::ADJUSTMENT_IN
                : StockMovementType::ADJUSTMENT_OUT;

            $movementNotes = "Reason: {$reason->label()}" . ($notes ? " - {$notes}" : '');

            $this->stockMovementService->recordMovement(
                product: $product,
                quantityChange: $signedQuantity,
                movementType: $movementType,
                userId: $userId,
                reference: $adjustment,
                referenceNumber: $adjustmentNumber,
                notes: $movementNotes
            );

            return $adjustment;
        });
    }

    /**
     * Generate unique adjustment number. Format: ADJ.YYMMDD.XXXX
     */
    private function generateAdjustmentNumber(): string
    {
        $prefix = 'ADJ.' . date('ymd') . '.';

        $latest = StockAdjustment::where('adjustment_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($latest->adjustment_number, -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}

