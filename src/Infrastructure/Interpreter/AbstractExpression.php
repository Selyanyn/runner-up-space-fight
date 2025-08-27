<?php

declare(strict_types=1);

namespace Hproject\Infrastructure\Interpreter;

abstract readonly class AbstractExpression
{
    abstract public function interpret(Context $context): mixed;
}
