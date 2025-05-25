<?php

namespace Hproject;

final readonly class WriteToLogCommand implements CommandInterface
{
    public function __construct(
        private \Throwable $exception,
        private Logger $logger,
    ) {}

    public function execute(): void
    {
        $this->logger->log($this->exception->getMessage());
    }
}