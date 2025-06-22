<?php

namespace Hproject\Queue;

use Hproject\CommandInterface;

final readonly class HardStopQueueProcessingCommand implements CommandInterface
{
    public function __construct(
        private CommandQueueInterface $queue,
    ) {}

    public function execute(): void
    {
        $this->queue->stopQueueProcessing();
    }
}