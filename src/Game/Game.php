<?php

namespace Hproject\Game;

use Hproject\Queue\CommandQueueInterface;

final readonly class Game
{
    public function __construct(
        public CommandQueueInterface $commandQueue,
        public GameState $gameState,
    ) {
        $this->commandQueue->beginQueueProcessing();
    }
}