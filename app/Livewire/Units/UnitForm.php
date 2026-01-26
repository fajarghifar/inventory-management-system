<?php

namespace App\Livewire\Units;

use Livewire\Component;
use App\Models\Unit;
use App\DTOs\UnitData;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Services\UnitService;
use App\Exceptions\UnitException;

class UnitForm extends Component
{
    public bool $isEditing = false;
    public ?Unit $unit = null;

    public string $name = '';
    public string $symbol = '';

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'name')->ignore($this->unit?->id),
            ],
            'symbol' => ['required', 'string', 'max:50'],
        ];
    }

    public function render()
    {
        return view('livewire.units.unit-form');
    }

    #[On('create-unit')]
    public function create(): void
    {
        $this->reset(['name', 'symbol', 'unit', 'isEditing']);
        $this->dispatch('open-modal', name: 'unit-form-modal');
    }

    #[On('edit-unit')]
    public function edit(Unit $unit): void
    {
        $this->unit = $unit;
        $this->name = $unit->name;
        $this->symbol = $unit->symbol;
        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'unit-form-modal');
    }

    public function save(UnitService $service): void
    {
        $this->validate();

        $data = new UnitData(
            name: $this->name,
            symbol: $this->symbol,
        );

        try {
            if ($this->isEditing && $this->unit) {
                $service->updateUnit($this->unit, $data);
                $message = 'Unit updated successfully.';
            } else {
                $service->createUnit($data);
                $message = 'Unit created successfully.';
            }

            $this->dispatch('close-modal', name: 'unit-form-modal');
            $this->dispatch('pg:eventRefresh-unit-table');
            $this->dispatch('toast', message: $message, type: 'success');
        } catch (UnitException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'An unexpected error occurred.', type: 'error');
        }
    }
}
