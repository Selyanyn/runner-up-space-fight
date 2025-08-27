<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Infrastructure\Math\FlatVector;

final readonly class ShootExpressionAstBuilder
{
    public static function build(
        int $gameObjectId,
        FlatVector $initialVelocity,
        int $damage,
    ): ShootExpression {
        return new ShootExpression(
            new TerminalGameObjectContextExpression($gameObjectId),
            new TerminalValueExpression($initialVelocity),
            new TerminalValueExpression($damage),
        );
    }
}
