<?php

declare(strict_types=1);

namespace Hproject\Infrastructure\Interpreter;

final class Context
{
    /**
     * @var array<non-empty-string, mixed>
     */
    private array $data;

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? throw new \InvalidArgumentException('Неизвестный ключ');
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }
}
