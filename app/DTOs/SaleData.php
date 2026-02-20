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
        public int $global_discount = 0,
    ) {}

    public static function fromArray(array $data): self
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
            global_discount: (int) ($data['global_discount'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'sale_date' => $this->sale_date->toDateTimeString(),
            'payment_method' => $this->payment_method->value,
            'created_by' => $this->created_by,
            'items' => array_map(fn($item) => $item->toArray(), $this->items),
            'customer_id' => $this->customer_id,
            'status' => $this->status->value,
            'notes' => $this->notes,
            'cash_received' => $this->cash_received,
            'change' => $this->change,
            'global_discount' => $this->global_discount,
        ];
    }
}
