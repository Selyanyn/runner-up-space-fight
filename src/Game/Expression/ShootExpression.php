<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Game\Command\MoveCommand;
use Hproject\Game\Game\Game;
use Hproject\Game\GameObject\PresentOnFieldInterface;
use Hproject\Game\GameObject\Projectile;
use Hproject\Infrastructure\Interpreter\AbstractExpression;
use Hproject\Infrastructure\Interpreter\Context;

final readonly class ShootExpression extends AbstractExpression
{
    public function __construct(
        public AbstractExpression $gameObjectExpression,
        public AbstractExpression $velocityExpression,
        public AbstractExpression $damageExpression,
    ) {
    }

    public function interpret(Context $context): mixed
    {
        /** @var Game $game */
        $game = $context->get('game_state');

        /** @var PresentOnFieldInterface $gameObject */
        $gameObject = $this->gameObjectExpression->interpret($context);

        $projectile = new Projectile(
            $game->gameState->generateNextId(),
            $gameObject->getLocation(),
            $this->velocityExpression->interpret($context),
            $this->damageExpression->interpret($context),
        );

        // Исходное перемещение снаряда - чтобы не столкнулся с испустившим его игровым объектом
        (new MoveCommand($projectile))->execute();

        $game->gameState->gameObjects[$projectile->getId()] = $projectile;

        return $projectile;
    }
}
