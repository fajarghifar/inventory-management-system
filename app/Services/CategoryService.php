<?php

namespace App\Services;

use Exception;
use App\Models\Category;
use App\DTOs\CategoryData;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CategoryException;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    /**
     * Create a new category.
     */
    public function createCategory(CategoryData $data): Category
    {
        return DB::transaction(function () use ($data) {
            try {
                $category = Category::create([
                    'name' => $data->name,
                    'slug' => $data->slug,
                    'description' => $data->description,
                ]);

                Cache::forget('categories_list_all');

                return $category;

            } catch (Exception $e) {
                throw CategoryException::creationFailed($e->getMessage(), [
                    'data' => (array) $data,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(Category $category, CategoryData $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            try {
                $category->update([
                    'name' => $data->name,
                    'slug' => $data->slug,
                    'description' => $data->description,
                ]);

                Cache::forget('categories_list_all');

                return $category->refresh();

            } catch (Exception $e) {
                throw CategoryException::updateFailed($e->getMessage(), [
                    'id'   => $category->id,
                    'data' => (array) $data
                ]);
            }
        });
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(Category $category): void
    {
        DB::transaction(function () use ($category) {
            try {
                if ($category->products()->exists()) {
                    throw new Exception("Cannot delete category because it is associated with products.");
                }

                $category->delete();

                Cache::forget('categories_list_all');

            } catch (Exception $e) {
                throw CategoryException::deletionFailed($e->getMessage(), ['id' => $category->id]);
            }
        });
    }
}
