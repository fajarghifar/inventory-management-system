<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\Attributes\On;

class CustomerDetail extends Component
{
    public ?Customer $customer = null;

    public function render()
    {
        return view('livewire.customers.customer-detail');
    }

    #[On('show-customer')]
    public function show(Customer $customer)
    {
        $this->customer = $customer;
        $this->dispatch('open-modal', name: 'customer-detail-modal');
    }

    public function edit()
    {
        if ($this->customer) {
            $this->dispatch('close-modal', name: 'customer-detail-modal');
            $this->dispatch('edit-customer', ['customer' => $this->customer->id]);
        }
    }
}
