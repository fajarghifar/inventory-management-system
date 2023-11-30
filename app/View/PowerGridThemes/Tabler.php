<?php

namespace App\View\PowerGridThemes;

use PowerComponents\LivewirePowerGrid\Themes\Theme;
use PowerComponents\LivewirePowerGrid\Themes\Bootstrap5;
use PowerComponents\LivewirePowerGrid\Themes\Components\{Actions,
    Checkbox,
    Cols,
    Editable,
    FilterBoolean,
    FilterDatePicker,
    FilterInputText,
    FilterMultiSelect,
    FilterNumber,
    FilterSelect,
    Footer,
    Radio,
    SearchBox,
    Table,
    Toggleable};

class Tabler extends Bootstrap5
{
    public string $name = 'bootstrap5';

    public function table(): Table
    {
        return Theme::table('table table-bordered card-table table-vcenter text-nowrap datatable')
            ->div('table-responsive')
            ->thead('')
            ->thAction('')
            ->tdAction('')
            ->tr('')
            ->th('')
            ->tbody('')
            ->tdBodyEmpty('', '')
            ->tdBodyTotalColumns('', '');
    }

    public function cols(): Cols
    {
        return Theme::cols()
            ->div('')
            ->clearFilter('' );
    }

    public function footer(): Footer
    {
        return Theme::footer()
            ->view($this->root() . '.footer')
            ->select('');
    }

    public function actions(): Actions
    {
        return Theme::actions()
            ->headerBtn('', '')
            ->tdBody('', '')
            ->rowsBtn('', '');
    }

    public function toggleable(): Toggleable
    {
        return Theme::toggleable()
            ->view($this->root() . '.toggleable');
    }

    public function editable(): Editable
    {
        return Theme::editable()
            ->view($this->root() . '.editable')
            ->span('d-flex justify-content-between')
            ->button('width: 100%;text-align: left;border: 0;padding: 4px;background: none')
            ->input('form-control shadow-none');
    }

    public function checkbox(): Checkbox
    {
        return Theme::checkbox()
            ->th('', 'font-size: 1rem !important;text-align:center')
            ->div('form-check')
            ->label('form-check-label')
            ->input('form-check-input shadow-none');
    }

    public function radio(): Radio
    {
        return Theme::radio()
            ->th('')
            ->label('form-check-label')
            ->input('form-check-input');
    }

    public function filterBoolean(): FilterBoolean
    {
        return Theme::filterBoolean()
            ->view($this->root() . '.filters.boolean')
            ->select('form-control form-select shadow-none');
    }

    public function filterDatePicker(): FilterDatePicker
    {
        return Theme::filterDatePicker()
            ->view($this->root() . '.filters.date-picker')
            ->input('form-control shadow-none');
    }

    public function filterMultiSelect(): FilterMultiSelect
    {
        return Theme::filterMultiSelect()
            ->view($this->root() . '.filters.multi-select');
    }

    public function filterNumber(): FilterNumber
    {
        return Theme::filterNumber()
            ->base(attrStyle: 'min-width: 85px !important')
            ->view($this->root() . '.filters.number')
            ->input('form-control shadow-none');
    }

    public function filterSelect(): FilterSelect
    {
        return Theme::filterSelect()
            ->view($this->root() . '.filters.select')
            ->select('form-control form-select shadow-none');
    }

    public function filterInputText(): FilterInputText
    {
        return Theme::filterInputText()
            ->base(attrStyle: 'min-width: 165px !important')
            ->view($this->root() . '.filters.input-text')
            ->select('form-control mb-1 shadow-none form-select')
            ->input('form-control shadow-none');
    }

    public function searchBox(): SearchBox
    {
        return Theme::searchBox()
            ->input('col-12 col-sm-8 form-control') // form-control form-control-sm
            ->iconSearch('bi bi-search')
            ->iconClose('');
    }
}
