<?php

namespace App\DTOs;

class ProductData
{
    public function __construct(
        public readonly int $category_id,
        public readonly int $unit_id,
        public readonly ?string $sku,
        public readonly string $name,
        public readonly float $purchase_price,
        public readonly float $selling_price,
        public readonly int $quantity,
        public readonly int $min_stock,
        public readonly bool $is_active,
        public readonly ?string $description,
        public readonly ?string $notes,
        public readonly ?string $image = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            category_id: (int) $data['category_id'],
            unit_id: (int) $data['unit_id'],
            sku: !empty($data['sku']) ? $data['sku'] : null,
            name: $data['name'],
            purchase_price: (float) $data['purchase_price'],
            selling_price: (float) $data['selling_price'],
            quantity: (int) ($data['quantity'] ?? 0),
            min_stock: (int) ($data['min_stock'] ?? 0),
            is_active: (bool) ($data['is_active'] ?? true),
            description: empty($data['description']) ? null : $data['description'],
            notes: empty($data['notes']) ? null : $data['notes'],
            image: !empty($data['image']) ? $data['image'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'unit_id' => $this->unit_id,
            'sku' => $this->sku,
            'name' => $this->name,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'quantity' => $this->quantity,
            'min_stock' => $this->min_stock,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'notes' => $this->notes,
            'image' => $this->image,
        ];
    }
}
