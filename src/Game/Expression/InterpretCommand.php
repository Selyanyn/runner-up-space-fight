<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Infrastructure\Command\CommandInterface;
use Hproject\Infrastructure\Interpreter\AbstractExpression;
use Hproject\Infrastructure\Interpreter\Context;
use Hproject\Infrastructure\IoC\InversionOfControlContainer;

final readonly class InterpretCommand implements CommandInterface
{
    /**
     * @param list<mixed> $arguments
     */
    public function __construct(
        private string $action,
        private Context $context,
        private array $arguments,
    ) {
    }

    public function execute(): void
    {
        /** @var AbstractExpression $expression */
        $expression = InversionOfControlContainer::resolve('action_'.$this->action, ...$this->arguments);
        $expression->interpret($this->context);
    }
}
