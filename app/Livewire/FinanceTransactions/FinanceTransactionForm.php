<?php

namespace App\Livewire\FinanceTransactions;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\DTOs\FinanceTransactionData;
use Illuminate\Support\Facades\Auth;
use App\Services\FinanceTransactionService;
use App\Exceptions\FinanceTransactionException;

class FinanceTransactionForm extends Component
{
    public ?FinanceTransaction $transaction = null;
    public bool $isEditing = false;

    public $transaction_date;
    public $type = 'expense'; // Default to expense
    public $finance_category_id;
    public $amount;
    public $description;
    public $external_reference;

    public array $categoryOptions = [];

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
        $this->loadOptions();
    }

    public function updatedType()
    {
        $this->finance_category_id = null;
        $this->loadOptions();
    }

    public function loadOptions()
    {
        $query = FinanceCategory::orderBy('name');

        if ($this->type) {
            $query->where('type', $this->type);
        }

        $this->categoryOptions = $query->get()->map(function ($c) {
            return ['value' => $c->id, 'label' => $c->name]; // Type is redundant in label if filtered
        })->toArray();
    }

    public function rules(): array
    {
        return [
            'transaction_date' => ['required', 'date'],
            'type' => ['required', 'in:income,expense'],
            'finance_category_id' => ['required', 'exists:finance_categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string'],
            'external_reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    #[On('create-finance-transaction')]
    public function create(): void
    {
        $this->reset(['transaction', 'isEditing', 'finance_category_id', 'amount', 'description', 'external_reference']);
        $this->type = 'expense'; // Reset to default
        $this->transaction_date = now()->format('Y-m-d');
        $this->loadOptions(); // Reload based on default type
        $this->dispatch('open-modal', name: 'finance-transaction-form-modal');
    }

    #[On('edit-finance-transaction')]
    public function edit(FinanceTransaction $transaction): void
    {
        if ($transaction->reference_type) {
            $this->dispatch('toast', message: 'System transactions (Sales/Purchases) cannot be edited.', type: 'error');
            return;
        }

        $this->transaction = $transaction;
        $this->isEditing = true;

        $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
        $this->type = $transaction->category->type->value;
        $this->loadOptions(); // Reload based on transaction type

        $this->finance_category_id = $transaction->finance_category_id;
        $this->amount = $transaction->amount;
        $this->description = $transaction->description;
        $this->external_reference = $transaction->external_reference;

        $this->dispatch('open-modal', name: 'finance-transaction-form-modal');
    }

    public function save(FinanceTransactionService $service): void
    {
        $this->validate();

        $data = new FinanceTransactionData(
            transaction_date: Carbon::parse($this->transaction_date),
            finance_category_id: (int) $this->finance_category_id,
            amount: (int) $this->amount,
            description: $this->description,
            external_reference: $this->external_reference,
            created_by: Auth::id() ?? 1, // Fallback for safety, though Auth check should be middleware
        );

        try {
            if ($this->isEditing && $this->transaction) {
                $service->updateTransaction($this->transaction, $data);
                $message = 'Transaction updated successfully.';
            } else {
                $service->createTransaction($data);
                $message = 'Transaction recorded successfully.';
            }

            $this->dispatch('close-modal', name: 'finance-transaction-form-modal');
            $this->dispatch('pg:eventRefresh-finance-transaction-table');
            $this->dispatch('toast', message: $message, type: 'success');
        } catch (FinanceTransactionException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error($e);
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.finance-transactions.finance-transaction-form', [
            'categories' => FinanceCategory::orderBy('name')->get(),
        ]);
    }
}
