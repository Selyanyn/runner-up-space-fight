<?php

namespace Hproject\Queue;

use Hproject\CommandInterface;

final readonly class SoftStopQueueProcessingCommand implements CommandInterface
{
    public function __construct(
        private CommandQueueInterface $queue,
    ) {}

    public function execute(): void
    {
        $queue = $this->queue;
        $this->queue->addBeforeCommandProcessingEvent(
            function () use ($queue) {
                if ($queue->isQueueEmpty()) {
                    $queue->stopQueueProcessing();
                }
            }
        );
    }
}