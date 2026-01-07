<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use Livewire\Component;
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

    public function edit()
    {
        if ($this->unit) {
            $this->dispatch('close-modal', name: 'unit-detail-modal');
            $this->dispatch('edit-unit', ['unit' => $this->unit->id]);
        }
    }
}
