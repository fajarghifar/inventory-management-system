<?php

namespace App\DTOs;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use Carbon\Carbon;

readonly class SaleData
{
    /**
     * @param SaleItemData[] $items
     */
    public function __construct(
        public Carbon $sale_date,
        public PaymentMethod $payment_method,
        public int $created_by,
        public array $items,
        public ?int $customer_id = null,
        public SaleStatus $status = SaleStatus::COMPLETED,
        public ?string $notes = null,
        public int $cash_received = 0,
        public int $change = 0,
        public int $cash = 0,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            sale_date: Carbon::parse($data['sale_date']),
            payment_method: PaymentMethod::from($data['payment_method']),
            created_by: (int) $data['created_by'],
            items: array_map(fn($item) => SaleItemData::fromArray($item), $data['items']),
            customer_id: isset($data['customer_id']) ? (int) $data['customer_id'] : null,
            status: isset($data['status']) ? SaleStatus::from($data['status']) : SaleStatus::COMPLETED,
            notes: $data['notes'] ?? null,
            cash_received: (int) ($data['cash_received'] ?? 0),
            change: (int) ($data['change'] ?? 0),
            cash: (int) ($data['cash'] ?? 0),
        );
    }
}
