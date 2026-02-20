<?php

namespace App\Http\Controllers\Api;

use App\Models\FinanceCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class FinanceCategoryController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q') ?? $request->input('search');
        $cacheKey = 'finance_categories_search_' . md5((string) $query);

        $categories = Cache::remember($cacheKey, 300, function () use ($query) {
            return FinanceCategory::query()
                ->when($query, function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get()
                ->map(function ($category) {
                    return [
                        'value' => $category->id,
                        'text' => $category->name . ' (' . ucfirst($category->type->value) . ')',
                        'name' => $category->name,
                    ];
                });
        });

        return response()->json($categories);
    }
}
