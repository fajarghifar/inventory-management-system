<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use Illuminate\Support\Str;
use App\DTOs\ProductData;
use App\Exceptions\ProductException;
use App\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    /**
     * Create a new product.
     */
    public function createProduct(ProductData $data): Product
    {
        return DB::transaction(function () use ($data) {
            try {
                $sku = $data->sku ?? $this->generateUniqueSku();

                $product = Product::create([
                    'category_id' => $data->category_id,
                    'unit_id' => $data->unit_id,
                    'sku' => $sku,
                    'name' => $data->name,
                    'purchase_price' => $data->purchase_price,
                    'selling_price' => $data->selling_price,
                    'quantity' => 0, // Set to 0 initially, updated by recordMovement
                    'min_stock' => $data->min_stock,
                    'is_active' => $data->is_active,
                    'description' => $data->description,
                    'notes' => $data->notes,
                ]);

                if ($data->quantity > 0) {
                    $this->stockMovementService->recordMovement(
                        product: $product,
                        quantityChange: (int) $data->quantity,
                        movementType: StockMovementType::OPENING_STOCK,
                        notes: 'Initial opening stock recorded on product creation'
                    );
                    $product->refresh();
                }

                return $product;

            } catch (Exception $e) {
                throw ProductException::creationFailed($e->getMessage(), [
                    'data' => (array) $data,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Product $product, ProductData $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            try {
                $product->update([
                    'category_id' => $data->category_id,
                    'unit_id' => $data->unit_id,
                    'sku' => $data->sku ?? $product->sku,
                    'name' => $data->name,
                    'purchase_price' => $data->purchase_price,
                    'selling_price' => $data->selling_price,
                    'quantity' => $data->quantity,
                    'min_stock' => $data->min_stock,
                    'is_active' => $data->is_active,
                    'description' => $data->description,
                    'notes' => $data->notes,
                ]);

                return $product->refresh();

            } catch (Exception $e) {
                throw ProductException::updateFailed($e->getMessage(), [
                    'id'   => $product->id,
                    'data' => (array) $data
                ]);
            }
        });
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            try {
                if ($product->purchaseItems()->exists() || $product->saleItems()->exists()) {
                    throw new Exception('Cannot delete product because it is associated with purchase or sale records.');
                }

                $product->delete();

            } catch (Exception $e) {
                throw ProductException::deletionFailed($e->getMessage(), ['id' => $product->id]);
            }
        });
    }

    /**
     * Generate a unique SKU in format P.YYMMDD.XXXX.
     */
    private function generateUniqueSku(): string
    {
        $prefix = 'P.' . date('ymd') . '.';

        do {
            $sku = $prefix . strtoupper(Str::random(4));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}
