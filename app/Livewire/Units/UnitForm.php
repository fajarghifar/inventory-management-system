<?php

namespace App\Livewire\Units;

use Exception;
use App\Models\Unit;
use App\DTOs\UnitData;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\UnitService;

class UnitForm extends Component
{
    public ?Unit $unit = null;

    public string $name = '';

    public string $symbol = '';

    public bool $isEditing = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:units,name,' . ($this->unit?->id)],
            'symbol' => ['required', 'string', 'max:10', 'unique:units,symbol,' . ($this->unit?->id)],
        ];
    }

    public function render()
    {
        return view('livewire.units.unit-form');
    }

    #[On('create-unit')]
    public function create(): void
    {
        $this->reset();
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'unit-modal');
    }

    #[On('edit-unit')]
    public function edit(Unit $unit): void
    {
        $this->resetValidation();
        $this->unit = $unit;
        $this->name = $unit->name;
        $this->symbol = $unit->symbol;

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'unit-modal');
    }

    public function save(UnitService $service): void
    {
        $validated = $this->validate($this->rules());

        try {
            $unitData = UnitData::fromArray($validated);

            if ($this->isEditing && $this->unit) {
                $service->updateUnit($this->unit, $unitData);
                $message = 'Unit updated successfully.';
            } else {
                $service->createUnit($unitData);
                $message = 'Unit created successfully.';
            }

            $this->dispatch('close-modal', name: 'unit-modal');
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('pg:eventRefresh-unit-table');
            $this->dispatch('toast', message: $message, type: 'success');
            $this->reset();

        } catch (Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
