<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Infrastructure\Interpreter\AbstractExpression;
use Hproject\Infrastructure\Interpreter\Context;

/**
 * Выражение листа дерева, извлекающего параметр из контекста.
 */
final readonly class TerminalContextExpression extends AbstractExpression
{
    public function __construct(
        private string $key,
    ) {
    }

    public function interpret(Context $context): mixed
    {
        return $context->get($this->key);
    }
}
