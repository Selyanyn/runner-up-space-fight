<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Game\Command\MoveCommand;
use Hproject\Game\Command\SetVelocityCommand;
use Hproject\Game\GameObject\MoveableAndVelocityChangeable;
use Hproject\Infrastructure\Command\MacroCommand;
use Hproject\Infrastructure\Interpreter\AbstractExpression;
use Hproject\Infrastructure\Interpreter\Context;
use Hproject\Infrastructure\Math\FlatVector;

final readonly class MoveExpression extends AbstractExpression
{
    public function __construct(
        public AbstractExpression $gameObjectExpression,
        public AbstractExpression $initialSpeedExpression,
    ) {
    }

    public function interpret(Context $context): mixed
    {
        /** @var MoveableAndVelocityChangeable $moveable */
        $moveable = $this->gameObjectExpression->interpret($context);
        /** @var FlatVector $velocity */
        $velocity = $this->initialSpeedExpression->interpret($context);

        $command = new MacroCommand([
            new SetVelocityCommand($moveable, $velocity),
            new MoveCommand($moveable),
        ]);
        $command->execute();

        return $moveable;
    }
}
