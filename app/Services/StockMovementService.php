<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\StockMovement;
use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    /**
     * Record a stock movement and update the product's quantity atomically.
     *
     * @param Product|int $product Product model or Product ID
     * @param int $quantityChange Positive for incoming (+), Negative for outgoing (-)
     * @param StockMovementType $movementType
     * @param int|null $userId
     * @param Model|null $reference Source model (Sale, Purchase, StockAdjustment)
     * @param string|null $referenceNumber External/Invoice/Adjustment number
     * @param string|null $notes
     * @return StockMovement
     * @throws Exception
     */
    public function recordMovement(
        Product|int $product,
        int $quantityChange,
        StockMovementType $movementType,
        ?int $userId = null,
        ?Model $reference = null,
        ?string $referenceNumber = null,
        ?string $notes = null
    ): StockMovement {
        $productId = $product instanceof Product ? $product->id : $product;
        $userId = $userId ?? Auth::id();

        // Lock the product row
        $productModel = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

        $before = (int) $productModel->quantity;
        $after = $before + $quantityChange;

        if ($after < 0 && $quantityChange < 0) {
            throw new Exception("Insufficient stock for product '{$productModel->name}'. Available: {$before}, Requested: " . abs($quantityChange));
        }

        // Update product quantity
        $productModel->quantity = $after;
        $productModel->save();

        // Create movement record
        return StockMovement::create([
            'product_id' => $productModel->id,
            'movement_type' => $movementType,
            'quantity' => $quantityChange,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference?->getKey(),
            'reference_number' => $referenceNumber,
            'user_id' => $userId,
            'notes' => $notes,
        ]);
    }
}

