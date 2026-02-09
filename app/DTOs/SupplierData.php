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
            email: empty($data['email']) ? null : $data['email'],
            phone: empty($data['phone']) ? null : $data['phone'],
            address: empty($data['address']) ? null : $data['address'],
            notes: empty($data['notes']) ? null : $data['notes'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'notes' => $this->notes,
        ];
    }
}
