<?php

declare(strict_types=1);

namespace Hproject\Authorization\User;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $login,
        public string $email,
        private string $passwordHash,
        private string $salt,
    ) {
    }

    public function isPasswordValid(string $password): bool
    {
        return hash('sha256', $password . $this->salt) === $this->passwordHash;
    }
}
