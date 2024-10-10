<?php

namespace App\Livewire\Tables;

use App\Models\Quotation;
use Livewire\Component;
use Livewire\WithPagination;

class QuotationTable extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $sortField = 'reference';

    public $sortAsc = false;

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;

        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function render()
    {
        return view('livewire.tables.quotation-table', [
            'quotations' => Quotation::query()
                ->with(['quotationDetails', 'customer'])
                ->search($this->search)
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate(),
        ]);
    }
}
