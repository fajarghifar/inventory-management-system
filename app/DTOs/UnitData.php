<?php

namespace App\DTOs;

class UnitData
{
    public function __construct(
        public readonly string $name,
        public readonly string $symbol,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            symbol: $data['symbol'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'symbol' => $this->symbol,
        ];
    }
}
