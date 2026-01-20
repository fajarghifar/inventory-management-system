<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use Illuminate\Support\Str;
use App\DTOs\ProductData;
use App\Exceptions\ProductException;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Create a new product.
     */
    public function createProduct(ProductData $data): Product
    {
        return DB::transaction(function () use ($data) {
            try {
                $sku = $data->sku ?? $this->generateUniqueSku();

                return Product::create([
                    'category_id' => $data->category_id,
                    'unit_id' => $data->unit_id,
                    'sku' => $sku,
                    'name' => $data->name,
                    'description' => $data->description,
                    'purchase_price' => $data->purchase_price,
                    'selling_price' => $data->selling_price,
                    'quantity' => $data->quantity,
                    'min_stock' => $data->min_stock,
                    'is_active' => $data->is_active,
                ]);

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
                    'sku' => $data->sku ?? $product->sku, // Should we allow updating SKU? Assuming yes if provided.
                    'name' => $data->name,
                    'description' => $data->description,
                    'purchase_price' => $data->purchase_price,
                    'selling_price' => $data->selling_price,
                    'quantity' => $data->quantity,
                    'min_stock' => $data->min_stock,
                    'is_active' => $data->is_active,
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
                if ($product->purchaseDetails()->exists() || $product->saleDetails()->exists()) {
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
