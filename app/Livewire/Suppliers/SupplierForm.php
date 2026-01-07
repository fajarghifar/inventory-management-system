<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

class SupplierForm extends Component
{
    public ?Supplier $supplier = null;

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|string|max:255')]
    public string $contact_person = '';

    #[Rule('nullable|email|max:255|unique:suppliers,email,except,id')]
    public string $email = '';

    #[Rule('nullable|string|max:20')]
    public string $phone = '';

    #[Rule('nullable|string|max:1000')]
    public string $address = '';

    #[Rule('nullable|string|max:1000')]
    public string $notes = '';

    public bool $isEditing = false;

    public function render()
    {
        return view('livewire.suppliers.supplier-form');
    }

    #[On('create-supplier')]
    public function create()
    {
        $this->reset();
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'supplier-modal');
    }

    #[On('edit-supplier')]
    public function edit(Supplier $supplier)
    {
        $this->resetValidation();
        $this->supplier = $supplier;
        $this->name = $supplier->name;
        $this->contact_person = $supplier->contact_person;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone ?? '';
        $this->address = $supplier->address ?? '';
        $this->notes = $supplier->notes ?? '';

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'supplier-modal');
    }

    public function save(SupplierService $service)
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . ($this->supplier?->id),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            if ($this->isEditing && $this->supplier) {
                $service->updateSupplier($this->supplier, $validated);
                $message = 'Supplier updated successfully.';
            } else {
                $service->createSupplier($validated);
                $message = 'Supplier created successfully.';
            }

            $this->dispatch('close-modal', name: 'supplier-modal');
            $this->dispatch('pg:eventRefresh-default'); // Clean way to refresh PowerGrid if using default tableName
            $this->dispatch('pg:eventRefresh-supplier-table'); // Specific table name
            $this->dispatch('toast', message: $message, type: 'success');
            $this->reset();

        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
