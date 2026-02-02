<?php

namespace App\Services;

use App\Models\FinanceTransaction;
use Illuminate\Support\Facades\DB;
use App\DTOs\FinanceTransactionData;
use App\Exceptions\FinanceTransactionException;

class FinanceTransactionService
{
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
            throw FinanceTransactionException::creationFailed($e->getMessage());
        }
    }

    public function updateTransaction(FinanceTransaction $transaction, FinanceTransactionData $data): FinanceTransaction
    {
        try {
            return DB::transaction(function () use ($transaction, $data) {
                $transaction->update([
                    'transaction_date' => $data->transaction_date,
                    'finance_category_id' => $data->finance_category_id,
                    'amount' => $data->amount,
                    'description' => $data->description,
                    'external_reference' => $data->external_reference,
                    // created_by is usually not updated, or only by admin. Let's assume immutable owner for now unless needed.
                ]);

                return $transaction->fresh();
            });
        } catch (\Exception $e) {
            throw FinanceTransactionException::updateFailed($e->getMessage());
        }
    }

    public function deleteTransaction(FinanceTransaction $transaction): void
    {
        try {
            DB::transaction(function () use ($transaction) {
                $transaction->delete();
            });
        } catch (\Exception $e) {
            throw FinanceTransactionException::deletionFailed($e->getMessage());
        }
    }

    private function generateTransactionCode(): string
    {
        $prefix = 'TRX.' . date('ymd') . '.';

        $latest = FinanceTransaction::where('code', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$latest) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($latest->code, -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
