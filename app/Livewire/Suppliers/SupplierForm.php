<?php

namespace App\Livewire\Suppliers;

use Exception;
use Livewire\Component;
use App\Models\Supplier;
use App\DTOs\SupplierData;
use Livewire\Attributes\On;
use App\Services\SupplierService;

class SupplierForm extends Component
{
    public ?Supplier $supplier = null;

    public string $name = '';

    public string $contact_person = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $notes = '';

    public bool $isEditing = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:suppliers,email,' . ($this->supplier?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function render()
    {
        return view('livewire.suppliers.supplier-form');
    }

    #[On('create-supplier')]
    public function create(): void
    {
        $this->reset();
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'supplier-modal');
    }

    #[On('edit-supplier')]
    public function edit(Supplier $supplier): void
    {
        $this->resetValidation();
        $this->supplier = $supplier;
        $this->name = $supplier->name;
        $this->contact_person = $supplier->contact_person;
        $this->email = $supplier->email ?? '';
        $this->phone = $supplier->phone ?? '';
        $this->address = $supplier->address ?? '';
        $this->notes = $supplier->notes ?? '';

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'supplier-modal');
    }

    public function save(SupplierService $service): void
    {
        $validated = $this->validate($this->rules());

        try {
            $supplierData = SupplierData::fromArray($validated);

            if ($this->isEditing && $this->supplier) {
                $service->updateSupplier($this->supplier, $supplierData);
                $message = 'Supplier updated successfully.';
            } else {
                $service->createSupplier($supplierData);
                $message = 'Supplier created successfully.';
            }

            $this->dispatch('close-modal', name: 'supplier-modal');
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('pg:eventRefresh-supplier-table');
            $this->dispatch('toast', message: $message, type: 'success');
            $this->reset();

        } catch (Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
