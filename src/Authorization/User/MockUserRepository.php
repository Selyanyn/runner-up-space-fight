<?php

declare(strict_types=1);

namespace Hproject\Authorization\User;

class MockUserRepository implements UserRepositoryInterface
{
    public function getByLogin(string $login): ?User
    {
        return match ($login) {
            'userA' => new User(
                id: 1,
                login: 'Alexander',
                email: 'alexander@example.com',
                passwordHash: 'bb51728e9e653cf318a092c1ce4c97d0a2250d0eb375cbc1d1e35bbde67b5da6',
                salt: 'rainbow',
            ),
            'userB' => new User(
                id: 2,
                login: 'Boris',
                email: 'boris@example.com',
                passwordHash: 'e4d2f949a401c04e9cd0bd410e31d6f81b413974151fc458c18d2d186c379219',
                salt: 'salt',
            ),
            default => null,
        };
    }
}
