<?php

namespace App\Livewire\FinanceTransactions;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\FinanceTransaction;

class FinanceTransactionDetail extends Component
{
    public ?FinanceTransaction $transaction = null;

    #[On('view-finance-transaction')]
    public function show(FinanceTransaction $transaction): void
    {
        $this->transaction = $transaction->load(['category', 'creator']);
        $this->dispatch('open-modal', name: 'finance-transaction-detail-modal');
    }

    public function closeModal()
    {
        $this->transaction = null;
    }

    public function render()
    {
        return view('livewire.finance-transactions.finance-transaction-detail');
    }
}
