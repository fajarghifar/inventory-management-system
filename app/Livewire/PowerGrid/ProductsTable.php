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
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center')
                ->searchable()
                ->sortable(),

            Column::make('Image', 'image')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center'),

            Column::make('Name', 'name')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Category')
                ->field('category_name')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center'),

            Column::make('Quantity', 'quantity')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center')
                ->sortable(),

            Column::make('Unit', 'unit_name')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center'),

            Column::make('Selling Price', 'selling_price')
                ->headerAttribute('align-middle text-center')
                ->bodyAttribute('align-middle text-center')
                ->sortable()
                ->searchable(),

            Column::action('Action')
                ->headerAttribute('align-middle text-center', styleAttr: 'width: 150px;')
                ->bodyAttribute('align-middle text-center d-flex justify-content-around')
        ];
    }

    public function filters(): array
    {
        return [
            //
        ];
    }

    public function actions(\App\Models\Product $row): array
    {
        return [
            Button::make('show', file_get_contents('assets/svg/eye.svg'))
                ->class('btn btn-outline-info btn-icon')
                ->tooltip('Show Product Details')
                ->route('products.show', ['product' => $row])
                ->method('get'),

            Button::make('edit', file_get_contents('assets/svg/edit.svg'))
                ->class('btn btn-outline-warning btn-icon')
                ->route('products.edit', ['product' => $row])
                ->method('get')
                ->tooltip('Edit Product'),

            Button::add('delete')
                ->slot(file_get_contents('assets/svg/trash.svg'))
                ->class('btn btn-outline-danger btn-icon')
                ->tooltip('Delete Product')
                ->route('products.destroy', ['product' => $row])
                ->method('delete'),
        ];
    }
}
