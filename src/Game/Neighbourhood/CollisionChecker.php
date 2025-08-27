<?php

declare(strict_types=1);

namespace Hproject\Game\Neighbourhood;

use Hproject\Game\Game\GameObjectInterface;
use Hproject\Infrastructure\Command\MacroCommand;
use Hproject\Infrastructure\Queue\CommandQueueInterface;

final readonly class CollisionChecker
{
    public function __construct(
        private NeighbourhoodSystem $originalSystem,
        private NeighbourhoodSystem $systemWithOffset,
        private CommandQueueInterface $commandQueue,
    ) {
    }

    public function checkCollision(GameObjectInterface $gameObject): void
    {
        $this->commandQueue->enqueue(
            new MacroCommand([
                new CheckGameObjectNeighbourhoodCommand(
                    $this->originalSystem,
                    $gameObject,
                    $this->commandQueue,
                ),
                new CheckGameObjectNeighbourhoodCommand(
                    $this->systemWithOffset,
                    $gameObject,
                    $this->commandQueue,
                ),
            ]),
        );
    }
}
