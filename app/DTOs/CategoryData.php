<?php

namespace App\DTOs;

use Illuminate\Support\Str;

class CategoryData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?string $description,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: !empty($data['slug']) ? $data['slug'] : Str::slug($data['name']),
            description: $data['description'] ?? null,
        );
    }
}
