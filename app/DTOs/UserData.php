<?php

namespace App\DTOs;

class UserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $username,
        public readonly string $email,
        public readonly ?string $password = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            username: $data['username'],
            email: $data['email'],
            password: $data['password'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
