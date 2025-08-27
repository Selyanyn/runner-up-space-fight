<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Infrastructure\Interpreter\AbstractExpression;
use Hproject\Infrastructure\Interpreter\Context;

/**
 * Выражение листа дерева, извлекающего параметр из контекста.
 */
final readonly class TerminalValueExpression extends AbstractExpression
{
    public function __construct(
        private mixed $value,
    ) {
    }

    public function interpret(Context $context): mixed
    {
        return $this->value;
    }
}
