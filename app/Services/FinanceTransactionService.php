<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Support\Str;
use App\Models\FinanceCategory;
use App\Enums\FinanceCategoryType;
use App\Models\FinanceTransaction;
use Illuminate\Support\Facades\DB;
use App\DTOs\FinanceTransactionData;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\FinanceTransactionException;

class FinanceTransactionService
{
    /**
     * Record income from a completed sale.
     */
    public function recordIncomeFromSale(Sale $sale): void
    {
        $category = $this->getOrCreateCategory('Product Sales', FinanceCategoryType::Income);

        FinanceTransaction::updateOrCreate(
            [
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
            ],
            [
                'code' => $this->generateTransactionCode('INC'),
                'transaction_date' => $sale->sale_date,
                'finance_category_id' => $category->id,
                'amount' => $sale->total,
                'description' => 'Sale Inv: ' . $sale->invoice_number . ' - ' . ($sale->customer->name ?? 'Guest'),
                'external_reference' => $sale->invoice_number,
                'created_by' => $sale->created_by ?? Auth::id() ?? 1,
            ]
        );
    }

    /**
     * Record expense from a paid purchase.
     */
    public function recordExpenseFromPurchase(Purchase $purchase): void
    {
        $category = $this->getOrCreateCategory('Product Purchases', FinanceCategoryType::Expense);

        FinanceTransaction::updateOrCreate(
            [
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
            ],
            [
                'code' => $this->generateTransactionCode('EXP'),
                'transaction_date' => $purchase->purchase_date,
                'finance_category_id' => $category->id,
                'amount' => $purchase->total,
                'description' => 'Purchase Inv: ' . $purchase->invoice_number . ' - ' . ($purchase->supplier->name ?? 'Unknown'),
                'external_reference' => $purchase->invoice_number,
                'created_by' => $purchase->created_by ?? Auth::id() ?? 1,
            ]
        );
    }

    /**
     * Void (delete) a transaction when the source is cancelled or deleted.
     */
    public function voidTransaction($model): void
    {
        FinanceTransaction::where('reference_type', get_class($model))
            ->where('reference_id', $model->id)
            ->delete();
    }

    /**
     * Create a manual finance transaction.
     */
    public function createTransaction(FinanceTransactionData $data): FinanceTransaction
    {
        try {
            return DB::transaction(function () use ($data) {
                return FinanceTransaction::create([
                    'code' => $this->generateTransactionCode(),
                    'transaction_date' => $data->transaction_date,
                    'finance_category_id' => $data->finance_category_id,
                    'amount' => $data->amount,
                    'description' => $data->description,
                    'external_reference' => $data->external_reference,
                    'created_by' => $data->created_by,
                ]);
            });
        } catch (\Exception $e) {
            throw new FinanceTransactionException('Failed to create transaction: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing manual finance transaction.
     */
    public function updateTransaction(FinanceTransaction $transaction, FinanceTransactionData $data): FinanceTransaction
    {
        if ($transaction->reference_type) {
            throw new FinanceTransactionException('Cannot update system-generated transaction (Sales/Purchases).');
        }

        try {
            return DB::transaction(function () use ($transaction, $data) {
                $transaction->update([
                    'transaction_date' => $data->transaction_date,
                    'finance_category_id' => $data->finance_category_id,
                    'amount' => $data->amount,
                    'description' => $data->description,
                    'external_reference' => $data->external_reference,
                ]);
                return $transaction;
            });
        } catch (\Exception $e) {
            throw new FinanceTransactionException('Failed to update transaction: ' . $e->getMessage());
        }
    }

    /**
     * Delete a finance transaction directly.
     */
    public function deleteTransaction(FinanceTransaction $transaction): void
    {
        if ($transaction->reference_type) {
            throw new FinanceTransactionException('Cannot delete system-generated transaction (Sales/Purchases). Please void the source instead.');
        }

        if ($transaction->category && in_array($transaction->category->name, ['Product Sales', 'Product Purchases'])) {
            throw new FinanceTransactionException('Cannot delete transactions belonging to protected categories (Product Sales/Product Purchases).');
        }

        try {
            $transaction->delete();
        } catch (\Exception $e) {
            throw new FinanceTransactionException('Failed to delete transaction: ' . $e->getMessage());
        }
    }

    private function getOrCreateCategory(string $name, FinanceCategoryType $type): FinanceCategory
    {
        return FinanceCategory::firstOrCreate(
            ['name' => $name],
            [
                'type' => $type,
                'slug' => Str::slug($name),
            ]
        );
    }

    private function generateTransactionCode(string $prefix = 'TRX'): string
    {
        return $prefix . '.' . now()->format('ymd') . '.' . strtoupper(Str::random(4));
    }
}
