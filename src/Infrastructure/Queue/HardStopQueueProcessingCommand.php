<?php

namespace Hproject\Infrastructure\Queue;

use Hproject\Infrastructure\Command\CommandInterface;

final readonly class HardStopQueueProcessingCommand implements CommandInterface
{
    public function execute(): void
    {
    }
}
