<?php

namespace Hproject\Infrastructure\Queue;

use Hproject\Infrastructure\Command\CommandInterface;

final readonly class ChangeQueueStateToMoveToCommand implements CommandInterface
{
    public function __construct(
        public CommandQueueInterface $moveToQueue,
    ) {
    }

    public function execute(): void
    {
    }
}
