<?php

namespace App\Livewire\PowerGrid;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridColumns;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ProductsTable extends PowerGridComponent
{
    public function setUp(): array
    {
        //$this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Product::query()
            ->with(['category', 'unit']);
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('image')
            ->addColumn('name')
            ->addColumn('category_id', function (Product $product){
                return $product->category_id;
            })
            ->addColumn('category_name', function (Product $product){
                return $product->category->name;
            })
            ->addColumn('quantity')
            ->addColumn('unit_id')
            ->addColumn('unit_name', function (Product $product){
                return $product->unit->short_code;
            })

            ->addColumn('selling_price');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Image', 'image'),

            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Category')
                ->field('category_name'),

            Column::make('Quantity', 'quantity')
                ->sortable(),

            Column::make('Unit', 'unit_name'),

            Column::make('Selling Price', 'selling_price')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            //
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(\App\Models\Product $row): array
    {
        return [
            Button::make('show', file_get_contents('assets/svg/eye.svg'))
//                ->slot('Show')
                ->class('btn btn-outline-info btn-icon w-100')
                ->tooltip('Show Product Details')
                ->route('products.show', ['product' => $row])
                ->method('get'),

            Button::make('edit', file_get_contents('assets/svg/edit.svg'))
                ->class('btn btn-outline-warning btn-icon w-100')
                ->route('products.edit', ['product' => $row])
                ->method('get')
                ->tooltip('Edit Product'),

            Button::add('delete')
                ->slot(file_get_contents('assets/svg/trash.svg'))
                ->class('btn btn-outline-danger btn-icon w-100')
                ->tooltip('Delete Product')
                ->route('products.destroy', ['product' => $row])
                ->method('delete'),
        ];
    }

    /*
    public function actionRules(\App\Models\Product $row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
