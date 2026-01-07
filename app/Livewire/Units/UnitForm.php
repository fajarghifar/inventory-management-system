<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use App\Services\UnitService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

class UnitForm extends Component
{
    public ?Unit $unit = null;

    #[Rule('required|string|max:50|unique:units,name,except,id')]
    public string $name = '';

    #[Rule('required|string|max:10|unique:units,symbol,except,id')]
    public string $symbol = '';

    public bool $isEditing = false;

    public function render()
    {
        return view('livewire.units.unit-form');
    }

    #[On('create-unit')]
    public function create()
    {
        $this->reset();
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'unit-modal');
    }

    #[On('edit-unit')]
    public function edit(Unit $unit)
    {
        $this->resetValidation();
        $this->unit = $unit;
        $this->name = $unit->name;
        $this->symbol = $unit->symbol;

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'unit-modal');
    }

    public function save(UnitService $service)
    {
        $validated = $this->validate([
            'name' => 'required|string|max:50|unique:units,name,' . ($this->unit?->id),
            'symbol' => 'required|string|max:10|unique:units,symbol,' . ($this->unit?->id),
        ]);

        try {
            if ($this->isEditing && $this->unit) {
                $service->updateUnit($this->unit, $validated);
                $message = 'Unit updated successfully.';
            } else {
                $service->createUnit($validated);
                $message = 'Unit created successfully.';
            }

            $this->dispatch('close-modal', name: 'unit-modal');
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('pg:eventRefresh-unit-table');
            $this->dispatch('toast', message: $message, type: 'success');
            $this->reset();

        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
