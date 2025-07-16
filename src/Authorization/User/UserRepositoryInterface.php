<?php

declare(strict_types=1);

namespace Hproject\Authorization\User;

interface UserRepositoryInterface
{
    public function getByLogin(string $login): ?User;
}
