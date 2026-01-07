<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Services\CustomerService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

class CustomerForm extends Component
{
    public ?Customer $customer = null;

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('nullable|email|max:255|unique:customers,email,except,id')]
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
        return view('livewire.customers.customer-form');
    }

    #[On('create-customer')]
    public function create()
    {
        $this->reset();
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'customer-modal');
    }

    #[On('edit-customer')]
    public function edit(Customer $customer)
    {
        $this->resetValidation();
        $this->customer = $customer;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone ?? '';
        $this->address = $customer->address ?? '';
        $this->notes = $customer->notes ?? '';

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'customer-modal');
    }

    public function save(CustomerService $service)
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . ($this->customer?->id),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            if ($this->isEditing && $this->customer) {
                $service->updateCustomer($this->customer, $validated);
                $message = 'Customer updated successfully.';
            } else {
                $service->createCustomer($validated);
                $message = 'Customer created successfully.';
            }

            $this->dispatch('close-modal', name: 'customer-modal');
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('pg:eventRefresh-customer-table');
            $this->dispatch('toast', message: $message, type: 'success');
            $this->reset();

        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
