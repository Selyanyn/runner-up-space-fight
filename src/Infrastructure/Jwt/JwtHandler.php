<?php

declare(strict_types=1);

namespace Hproject\Infrastructure\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final readonly class JwtHandler
{
    public function __construct(
        private string $key,
    ) {
    }

    /**
     * Валидирует JWT-токен. Возвращает userId в случае успеха.
     *
     * @param null|callable(\stdClass $jwtData): void $claims
     */
    public function validateAndCheckClaims(string $jwtTokenRaw, ?callable $claims): int
    {
        $jwtToken = JWT::decode(
            $jwtTokenRaw,
            new Key(
                keyMaterial: $this->key,
                algorithm: 'HS256',
            ),
        );

        if ($jwtToken->exp < time()) {
            throw new UnauthorizedHttpException('Не авторизован');
        }

        if ($claims !== null) {
            $claims($jwtToken);
        }

        return $jwtToken->userId;
    }

    /**
     * Создаёт JWT-токен по указанному телу.
     *
     * @param array<non-empty-string, mixed> $data
     * @return non-empty-string
     */
    public function createJwtToken(array $data): string
    {
        return JWT::encode(
            [
                'iat' => (new \DateTimeImmutable())->getTimestamp(),
                'exp' => (new \DateTimeImmutable())->getTimestamp() + 86400,
                ...$data,
            ],
            $this->key,
            'HS256',
        );
    }
}
