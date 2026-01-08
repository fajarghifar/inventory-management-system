<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    /**
     * Create a new product.
     *
     * @param array $data
     * @return Product
     * @throws Exception
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            try {
                Log::info('Attempting to create product', ['sku' => $data['sku']]);

                if (empty($data['sku'])) {
                    $data['sku'] = $this->generateUniqueSku();
                }

                $product = Product::create([
                    'category_id'    => $data['category_id'],
                    'unit_id'        => $data['unit_id'],
                    'sku'            => $data['sku'],
                    'name'           => $data['name'],
                    'description'    => $data['description'] ?? null,
                    'purchase_price' => $data['purchase_price'],
                    'selling_price'  => $data['selling_price'],
                    'quantity'       => $data['quantity'] ?? 0,
                    'min_stock'      => $data['min_stock'] ?? 0,
                    'is_active'      => $data['is_active'] ?? true,
                ]);

                Log::info('Product created successfully', ['id' => $product->id]);

                return $product;

            } catch (Exception $e) {
                Log::error('Failed to create product: ' . $e->getMessage(), [
                    'data'  => $data,
                    'trace' => $e->getTraceAsString()
                ]);

                throw new Exception('Failed to create product. Please try again later.');
            }
        });
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     * @throws Exception
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            try {
                Log::info('Attempting to update product', ['id' => $product->id]);

                $product->update([
                    'category_id'    => $data['category_id'] ?? $product->category_id,
                    'unit_id'        => $data['unit_id'] ?? $product->unit_id,
                    'sku'            => $data['sku'] ?? $product->sku,
                    'name'           => $data['name'] ?? $product->name,
                    'description'    => array_key_exists('description', $data) ? $data['description'] : $product->description,
                    'purchase_price' => $data['purchase_price'] ?? $product->purchase_price,
                    'selling_price'  => $data['selling_price'] ?? $product->selling_price,
                    'quantity'       => $data['quantity'] ?? $product->quantity,
                    'min_stock'      => $data['min_stock'] ?? $product->min_stock,
                    'is_active'      => $data['is_active'] ?? $product->is_active,
                ]);

                Log::info('Product updated successfully', ['id' => $product->id]);

                return $product->refresh();

            } catch (Exception $e) {
                Log::error('Failed to update product: ' . $e->getMessage(), [
                    'id'   => $product->id,
                    'data' => $data
                ]);

                throw new Exception('Failed to update product information.');
            }
        });
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return void
     * @throws Exception
     */
    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            try {
                if ($product->purchaseDetails()->exists()) {
                    throw new Exception('Cannot delete product because it is associated with purchase records.');
                }

                Log::info('Attempting to delete product', ['id' => $product->id]);

                $product->delete();

                Log::info('Product deleted successfully', ['id' => $product->id]);

            } catch (Exception $e) {
                Log::error('Failed to delete product: ' . $e->getMessage(), ['id' => $product->id]);
                throw new Exception('Failed to delete product.');
            }
        });
    }

    /**
     * Generate a unique SKU in format XXX-XXXX-XXXX.
     *
     * @return string
     */
    private function generateUniqueSku(): string
    {
        do {
            $sku = strtoupper(Str::random(3) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}
