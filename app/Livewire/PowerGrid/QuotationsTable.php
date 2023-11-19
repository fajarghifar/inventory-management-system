<?php

namespace App\Livewire\PowerGrid;

use App\Models\Quotation;
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

final class QuotationsTable extends PowerGridComponent
{
    use WithExport;

    //public bool $multiSort = true;
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public int $perPage = 5;
    public array $perPageValues = [0, 5, 10, 20, 50];

    public function setUp(): array
    {
        //$this->showCheckBox();

        return [
//            Exportable::make('export')
//                ->striped()
//                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            Header::make()->showSearchInput(),
            Footer::make()
                ->showRecordCount('full')
                ->showPerPage($this->perPage, $this->perPageValues),

        ];
    }

    public function datasource(): Builder
    {
        return Quotation::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('reference')
            ->addColumn('date_formatted', fn (Quotation $model) => Carbon::parse($model->date)->format('d/m/Y'))

            ->addColumn('customer_id')
            ->addColumn('customer_name')

            ->addColumn('total_amount')
            ->addColumn('status');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Reference', 'reference')
                ->searchable(),
            Column::make('Date', 'date_formatted', 'date')
                ->sortable(),

            Column::make('Customer id', 'customer_id'),
            Column::make('Customer name', 'customer_name')
                ->sortable()
                ->searchable(),

            Column::make('Total amount', 'total_amount')
                ->headerAttribute('text-center align-middle')
                ->bodyAttribute('text-center align-middle'),


            Column::make('Status', 'status')
                ->headerAttribute('text-center align-middle')
                ->bodyAttribute('text-center align-middle')
                ->contentClasses([
                    'Pending' => 'badge bg-yellow text-white',
                    'Sent'    => 'badge bg-green text-white'
                ])
                ->sortable()
                ->searchable(),

            Column::action('Action')
                ->headerAttribute('text-center align-middle')
                ->bodyAttribute('text-center align-middle')
        ];
    }

    public function filters(): array
    {
        return [
//            Filter::datepicker('date'),
//            Filter::inputText('reference')->operators(['contains']),
//            Filter::inputText('customer_name')->operators(['contains']),
//            Filter::inputText('status')->operators(['contains']),
//            Filter::datetimepicker('created_at'),
//            Filter::datetimepicker('updated_at'),
//            Filter::datetimepicker('created_at'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(\App\Models\Quotation $row): array
    {
        return [
            Button::make('show', file_get_contents('assets/svg/eye.svg'))
                ->class('btn btn-outline-info btn-icon w-100')
                ->tooltip('Show Quotation Details')
                ->route('quotations.show', ['quotation' => $row])
                ->method('get'),

            Button::make('edit', file_get_contents('assets/svg/edit.svg'))
                ->class('btn btn-outline-warning btn-icon w-100')
                ->route('quotations.edit', ['quotation' => $row])
                ->method('get')
                ->tooltip('Edit Quotation'),

            Button::add('delete')
                ->slot(file_get_contents('assets/svg/trash.svg'))
                ->class('btn btn-outline-danger btn-icon w-100')
                ->tooltip('Delete Quotation')
                ->route('quotations.destroy', ['quotation' => $row])
                ->method('delete'),
        ];
    }

    /*
    public function actionRules($row): array
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
