<?php

namespace App\DTOs;

class SupplierData
{
    public function __construct(
        public readonly string $name,
        public readonly string $contact_person,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $address,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            contact_person: $data['contact_person'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

}
