<?php

namespace App\DTOs;

use App\Enums\FinanceCategoryType;

class FinanceCategoryData
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly FinanceCategoryType $type,
        public readonly ?string $description = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            type: $data['type'] instanceof FinanceCategoryType ? $data['type'] : FinanceCategoryType::from($data['type']),
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type->value,
            'description' => $this->description,
        ];
    }
}
