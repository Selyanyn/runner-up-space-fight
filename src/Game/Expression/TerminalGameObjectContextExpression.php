<?php

declare(strict_types=1);

namespace Hproject\Game\Expression;

use Hproject\Game\Game\Game;
use Hproject\Infrastructure\Interpreter\AbstractExpression;
use Hproject\Infrastructure\Interpreter\Context;

/**
 * Выражение листа дерева, извлекающего параметр из контекста.
 */
final readonly class TerminalGameObjectContextExpression extends AbstractExpression
{
    public function __construct(
        private int $id,
    ) {
    }

    public function interpret(Context $context): mixed
    {
        /** @var Game $game */
        $game = $context->get('game');
        /** @var positive-int $userId */
        $userId = $context->get('userId');

        $gameObject = $game->gameState->getGameObject($this->id, [$userId]);
        if (!$gameObject) {
            throw new \InvalidArgumentException('Не существует игрового объекта '.$this->id);
        }

        return $gameObject;
    }
}
