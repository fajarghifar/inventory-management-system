<?php

namespace App\DTOs;

use App\Enums\PurchaseStatus;
use Illuminate\Support\Carbon;

class PurchaseData
{
    public function __construct(
        public int $supplier_id,
        public Carbon $purchase_date,
        public array $items,
        public ?string $invoice_number = null,
        public ?Carbon $due_date = null,
        public ?string $notes = null,
        public ?string $proof_image = null,
        public PurchaseStatus $status = PurchaseStatus::DRAFT,
    ) {}

    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn(array $item) => PurchaseItemData::fromArray($item),
            $data['items'] ?? []
        );

        return new self(
            supplier_id: (int) $data['supplier_id'],
            purchase_date: Carbon::parse($data['purchase_date']),
            items: $items,
            invoice_number: $data['invoice_number'] ?? null,
            due_date: isset($data['due_date']) ? Carbon::parse($data['due_date']) : null,
            notes: $data['notes'] ?? null,
            proof_image: $data['proof_image'] ?? null,
            status: isset($data['status']) ? PurchaseStatus::from($data['status']) : PurchaseStatus::DRAFT,
        );
    }

    public function toArray(): array
    {
        return [
            'supplier_id' => $this->supplier_id,
            'purchase_date' => $this->purchase_date->toDateTimeString(),
            'items' => array_map(fn($item) => $item->toArray(), $this->items),
            'invoice_number' => $this->invoice_number,
            'due_date' => $this->due_date?->toDateTimeString(),
            'notes' => $this->notes,
            'proof_image' => $this->proof_image,
            'status' => $this->status->value,
        ];
    }
}
