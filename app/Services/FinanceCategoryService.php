<?php

namespace App\Services;

use App\Models\FinanceCategory;
use App\DTOs\FinanceCategoryData;
use App\Exceptions\FinanceCategoryException;
use Illuminate\Support\Facades\DB;

class FinanceCategoryService
{
    public function createCategory(FinanceCategoryData $data): FinanceCategory
    {
        try {
            return DB::transaction(function () use ($data) {
                return FinanceCategory::create([
                    'name' => $data->name,
                    'slug' => $data->slug,
                    'type' => $data->type,
                    'description' => $data->description,
                ]);
            });
        } catch (\Exception $e) {
            throw FinanceCategoryException::creationFailed($e->getMessage());
        }
    }

    public function updateCategory(FinanceCategory $category, FinanceCategoryData $data): FinanceCategory
    {
        try {
            return DB::transaction(function () use ($category, $data) {
                $category->update([
                    'name' => $data->name,
                    'slug' => $data->slug,
                    'type' => $data->type,
                    'description' => $data->description,
                ]);

                return $category->fresh();
            });
        } catch (\Exception $e) {
            throw FinanceCategoryException::updateFailed($e->getMessage());
        }
    }

    public function deleteCategory(FinanceCategory $category): void
    {
        try {
            DB::transaction(function () use ($category) {
                // Check for dependencies if needed (e.g. transactions)
                $category->delete();
            });
        } catch (\Exception $e) {
            throw FinanceCategoryException::deletionFailed($e->getMessage());
        }
    }
}
