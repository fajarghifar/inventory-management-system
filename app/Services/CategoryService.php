<?php

namespace App\Services;

use Exception;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     * @throws Exception
     */
    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            try {
                Log::info('Attempting to create category', ['name' => $data['name']]);

                // Auto-generate slug if not provided or empty
                if (empty($data['slug'])) {
                    $data['slug'] = Str::slug($data['name']);
                }

                $category = Category::create([
                    'name'        => $data['name'],
                    'slug'        => $data['slug'],
                    'description' => $data['description'] ?? null,
                ]);

                Log::info('Category created successfully', ['id' => $category->id]);

                return $category;

            } catch (Exception $e) {
                Log::error('Failed to create category: ' . $e->getMessage(), [
                    'data'  => $data,
                    'trace' => $e->getTraceAsString()
                ]);

                throw new Exception('Failed to create category. Please try again later.');
            }
        });
    }

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array $data
     * @return Category
     * @throws Exception
     */
    public function updateCategory(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            try {
                Log::info('Attempting to update category', ['id' => $category->id]);

                if (isset($data['name']) && empty($data['slug'])) {
                    $data['slug'] = Str::slug($data['name']);
                }

                $category->update([
                    'name'        => $data['name'] ?? $category->name,
                    'slug'        => $data['slug'] ?? $category->slug,
                    'description' => array_key_exists('description', $data) ? $data['description'] : $category->description,
                ]);

                Log::info('Category updated successfully', ['id' => $category->id]);

                return $category->refresh();

            } catch (Exception $e) {
                Log::error('Failed to update category: ' . $e->getMessage(), [
                    'id'   => $category->id,
                    'data' => $data
                ]);

                throw new Exception('Failed to update category information.');
            }
        });
    }

    /**
     * Delete a category.
     *
     * @param Category $category
     * @return void
     * @throws Exception
     */
    public function deleteCategory(Category $category): void
    {
        DB::transaction(function () use ($category) {
            try {
                // Future robustness: Check for related products
                // if ($category->products()->exists()) { throw new Exception("Cannot delete category with products."); }

                Log::info('Attempting to delete category', ['id' => $category->id]);

                $category->delete();

                Log::info('Category deleted successfully', ['id' => $category->id]);

            } catch (Exception $e) {
                Log::error('Failed to delete category: ' . $e->getMessage(), ['id' => $category->id]);
                throw new Exception('Failed to delete category.');
            }
        });
    }
}
