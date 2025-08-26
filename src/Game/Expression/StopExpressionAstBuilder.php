<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Infrastructure\Math\FlatVector;

final readonly class StopExpressionAstBuilder
{
    public static function build(
        int $gameObjectId,
    ): StopExpression {
        return new StopExpression(
            new TerminalContextExpression('game_object_'.$gameObjectId),
        );
    }
}
