<?php

namespace App\DTOs;

use Carbon\Carbon;

readonly class FinanceTransactionData
{
    public function __construct(
        public Carbon $transaction_date,
        public int $finance_category_id,
        public int $amount,
        public ?string $description,
        public ?string $external_reference,
        public int $created_by,
    ) {}

    public function toArray(): array
    {
        return [
            'transaction_date' => $this->transaction_date->format('Y-m-d'),
            'finance_category_id' => $this->finance_category_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'external_reference' => $this->external_reference,
            'created_by' => $this->created_by,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            transaction_date: Carbon::parse($data['transaction_date']),
            finance_category_id: (int) $data['finance_category_id'],
            amount: (int) $data['amount'],
            description: $data['description'] ?? null,
            external_reference: $data['external_reference'] ?? null,
            created_by: (int) ($data['created_by'] ?? \Illuminate\Support\Facades\Auth::id()),
        );
    }
}
