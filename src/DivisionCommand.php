<?php

namespace Hproject;

use RuntimeException;

final readonly class DivisionCommand implements CommandInterface
{
    public function __construct(
        private int $a,
        private int $b,
    ) {}

    public function execute(): void
    {
        if ($this->b === 0) {
            throw new RuntimeException('Невозможно произвести деление на 0');
        }
        echo $this->a / $this->b;
    }
}