<?php

namespace App\Livewire\Units;

use Livewire\Component;
use App\Models\Unit;
use Livewire\Attributes\On;

class UnitDetail extends Component
{
    public ?Unit $unit = null;

    public function render()
    {
        return view('livewire.units.unit-detail');
    }

    #[On('show-unit')]
    public function show(Unit $unit)
    {
        $this->unit = $unit;
        $this->dispatch('open-modal', name: 'unit-detail-modal');
    }

    public function closeModal()
    {
        $this->unit = null;
    }
}
