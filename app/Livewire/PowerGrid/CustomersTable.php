<?php

namespace App\Livewire\PowerGrid;

use App\Models\Customer;
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
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class CustomersTable extends PowerGridComponent
{
    use WithExport;

    public int $perPage = 5;
    public array $perPageValues = [0, 5, 10, 20, 50];

    public function setUp(): array
    {
        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            Header::make()->showSearchInput(),

            Footer::make()
                ->showPerPage($this->perPage, $this->perPageValues)
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Customer::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('name')

           /** Example of custom column using a closure **/
            ->addColumn('name_lower', fn (Customer $model) => strtolower(e($model->name)))

            ->addColumn('email')
            ->addColumn('phone')
            ->addColumn('address')
            ->addColumn('photo')
            ->addColumn('account_holder')
            ->addColumn('account_number')
            ->addColumn('bank_name')
            ->addColumn('created_at_formatted', fn (Customer $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left'),
            Column::make('Name', 'name')
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left')
                ->sortable()
                ->searchable(),
            Column::make('Email', 'email')
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left')
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

        ];
    }

    public function actions(\App\Models\Customer $row): array
    {
        return [
            Button::make('show', file_get_contents('assets/svg/eye.svg'))
                ->class('btn btn-outline-info btn-icon w-100')
                ->tooltip('Show Customer Details')
                ->route('customers.show', ['customer' => $row])
                ->method('get'),

            Button::make('edit', file_get_contents('assets/svg/edit.svg'))
                ->class('btn btn-outline-warning btn-icon w-100')
                ->route('customers.edit', ['customer' => $row])
                ->method('get')
                ->tooltip('Edit Customer'),

            Button::add('delete')
                ->slot(file_get_contents('assets/svg/trash.svg'))
                ->class('btn btn-outline-danger btn-icon w-100')
                ->tooltip('Delete Customer')
                ->route('customers.destroy', ['customer' => $row])
                ->method('delete'),
        ];
    }
}
