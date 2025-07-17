<?php

namespace Hproject\Infrastructure\Queue;

use Hproject\Infrastructure\Command\CommandInterface;

final readonly class SoftStopQueueProcessingCommand implements CommandInterface
{
    public function execute(): void
    {
    }
}
