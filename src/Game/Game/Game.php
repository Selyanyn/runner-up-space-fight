<?php

namespace Hproject\Game\Game;

use Hproject\Infrastructure\Queue\CommandQueueInterface;

final readonly class Game
{
    /**
     * @param CommandQueueInterface $commandQueue
     * @param GameState $gameState
     * @param non-empty-list<positive-int> $playersIds
     */
    public function __construct(
        public int $id,
        public CommandQueueInterface $commandQueue,
        public GameState $gameState,
        public array $playersIds,
    ) {
        $this->commandQueue->beginQueueProcessing();
    }
}
