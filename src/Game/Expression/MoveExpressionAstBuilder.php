<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Infrastructure\Math\FlatVector;

final readonly class MoveExpressionAstBuilder
{
    public static function build(
        int $gameObjectId,
        FlatVector $initialVelocity,
    ): MoveExpression {
        return new MoveExpression(
            new TerminalContextExpression('game_object_'.$gameObjectId),
            new TerminalValueExpression($initialVelocity)
        );
    }
}
