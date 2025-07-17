<?php

declare(strict_types=1);

namespace Hproject\Infrastructure\Queue;

use Hproject\Infrastructure\Command\CommandInterface;

interface CommandQueueStateInterface
{
    public function execute(CommandInterface $command): ?CommandQueueStateInterface;
    public function afterCommandExecution(CommandQueueInterface $commandQueue): ?CommandQueueStateInterface;
}
