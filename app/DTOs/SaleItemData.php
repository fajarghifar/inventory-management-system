<?php

namespace App\DTOs;

readonly class SaleItemData
{
    public function __construct(
        public int $product_id,
        public int $quantity,
        public int $unit_price, // Enforce integer for currency (e.g. Rupiah via casts)
        public int $discount = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: (int) $data['product_id'],
            quantity: (int) $data['quantity'],
            unit_price: (int) $data['unit_price'],
            discount: (int) ($data['discount'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'discount' => $this->discount,
        ];
    }
}
