<?php

namespace Hproject;

final readonly class EnqueueCommandCommand implements CommandInterface
{
    public function __construct(
        public CommandQueue $queue,
        public CommandInterface $command,
        public Logger $logger,
        public int $tries = 0,
    ) {}

    public function execute(): void
    {
        $this->queue->enqueue($this->command, $this->tries);
        $this->logger->log('В очередь установлена команда ' . $this->command::class . ', повторов: ' . $this->tries);
    }
}