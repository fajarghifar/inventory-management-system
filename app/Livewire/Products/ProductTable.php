<?php

namespace App\Livewire\Products;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use App\Exceptions\ProductException;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class ProductTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'product-table';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('product_export_' . now()->format('Y_m_d'))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage(perPage: 10, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Product::query()
            ->with(['category', 'unit']);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('sku')
            ->add('name')
            ->add('category_name', fn(Product $model) => $model->category ? $model->category->name : '-')
            ->add('unit_symbol', fn(Product $model) => $model->unit ? $model->unit->symbol : '-')
            ->add('purchase_price_formatted', fn(Product $model) => 'Rp ' . number_format($model->purchase_price, 0, ',', '.'))
            ->add('selling_price_formatted', fn(Product $model) => 'Rp ' . number_format($model->selling_price, 0, ',', '.'))
            ->add('margin_formatted', function(Product $model) {
                // Calculate margin
                $margin = $model->selling_price - $model->purchase_price;
                $percentage = $model->purchase_price > 0 ? ($margin / $model->purchase_price) * 100 : 0;

                // Format with percentage
                return 'Rp ' . number_format($margin, 0, ',', '.') .
                    ' <span class="text-xs text-gray-500">(' . round($percentage, 1) . '%)</span>';
            })
            ->add('quantity')
            ->add('min_stock')
            ->add('is_active_label', function(Product $model) {
                return $model->is_active
                    ? '<div class="flex items-center justify-center text-green-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></div>'
                    : '<div class="flex items-center justify-center text-red-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></div>';
            })
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::action('Action'),

            Column::make('ID', 'id')
                ->hidden()
                ->visibleInExport(true),

            Column::make('SKU', 'sku')
                ->searchable(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Category', 'category_name', 'category_id')
                ->sortable()
                ->searchable(),

            Column::make('Unit', 'unit_symbol', 'unit_id')
                ->sortable()
                ->searchable(),

            Column::make('Buying Price', 'purchase_price_formatted', 'purchase_price')
                ->sortable()
                ->bodyAttribute('text-right'),

            Column::make('Selling Price', 'selling_price_formatted', 'selling_price')
                ->sortable()
                ->bodyAttribute('text-right'),

            Column::make('Margin', 'margin_formatted')
                ->bodyAttribute('text-right text-indigo-600'),

            Column::make('Qty', 'quantity')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::make('Min Qty', 'min_stock')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::make('Status', 'is_active_label', 'is_active')
                ->sortable()
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::multiSelect('category_name', 'category_id')
                ->dataSource(Category::all())
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::multiSelect('unit_symbol', 'unit_id')
                ->dataSource(Unit::all()->map(function ($unit) {
                    return [
                        'id' => $unit->id,
                        'name_with_symbol' => "{$unit->name} ({$unit->symbol})"
                    ];
                }))
                ->optionLabel('name_with_symbol')
                ->optionValue('id'),

            Filter::boolean('is_active_label', 'is_active')
                ->label('Active', 'Inactive'), // Retain filter labels
        ];
    }

    public function actions(Product $row): array
    {
        // Use the new fields but keep the styled DeleteModal
        return [
            Button::add('view')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>')
                ->class('bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('show-product', ['product' => $row->id])
                ->tooltip('View Product'),

            Button::add('edit')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>')
                ->class('bg-amber-500 hover:bg-amber-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('edit-product', ['product' => $row->id])
                ->tooltip('Edit Product'),

            Button::add('delete')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>')
                ->class('bg-red-500 hover:bg-red-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('open-delete-modal', [
                    'component' => 'products.product-table',
                    'method' => 'delete',
                    'params' => ['rowId' => $row->id],
                    'title' => 'Delete Product?',
                    'description' => "Are you sure you want to delete product '{$row->name}'? This action cannot be undone.",
                ])
                ->tooltip('Delete Product'),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId, ProductService $service): void
    {
        $product = Product::find($rowId);

        if ($product) {
            try {
                $service->deleteProduct($product);
                $this->dispatch('toast', message: 'Product deleted successfully.', type: 'success');
            } catch (\Exception $e) {
                $message = $e instanceof ProductException
                    ? $e->getMessage()
                    : 'Failed to delete product: ' . $e->getMessage();

                $this->dispatch('toast', message: $message, type: 'error');
            }
        }
    }
}
