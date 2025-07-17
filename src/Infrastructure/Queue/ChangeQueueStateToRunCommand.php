<?php

namespace Hproject\Infrastructure\Queue;

use Hproject\Infrastructure\Command\CommandInterface;

final readonly class ChangeQueueStateToRunCommand implements CommandInterface
{
    public function execute(): void
    {
    }
}
