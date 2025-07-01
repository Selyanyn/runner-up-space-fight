<?php

namespace Hproject\Interpretate;

use Hproject\CommandInterface;

final readonly class InterpretateCommand implements CommandInterface
{
    public function __construct(
        private CommandQueueInterface $queue,
    ) {}

    public function execute(): void
    {
        $this->queue->beginQueueProcessing();
    }
}