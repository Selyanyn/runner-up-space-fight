<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Infrastructure\IoC\InversionOfControlContainer;

final readonly class InitIoCContainerInterpret
{
    public static function init(): void
    {
        InversionOfControlContainer::resolve(
            'register',
            'action_move',
            MoveExpressionAstBuilder::build(...),
        );

        InversionOfControlContainer::resolve(
            'register',
            'action_stop',
            StopExpressionAstBuilder::build(...),
        );

        InversionOfControlContainer::resolve(
            'register',
            'action_shoot',
            ShootExpressionAstBuilder::build(...),
        );
    }
}
