<?php

declare(strict_types=1);

namespace Hproject\Infrastructure\Queue;

use Hproject\Infrastructure\Command\CommandInterface;

final class RunCommandQueueState implements CommandQueueStateInterface
{
    private bool $stopUponExhaustingQueue = false;

    public function execute(CommandInterface $command): ?CommandQueueStateInterface
    {
        if ($command instanceof HardStopQueueProcessingCommand) {
            return null;
        }

        if ($command instanceof SoftStopQueueProcessingCommand) {
            $this->stopUponExhaustingQueue = true;
            return $this;
        }

        if ($command instanceof ChangeQueueStateToMoveToCommand) {
            return new MoveToCommandQueueState($command->moveToQueue);
        }

        $command->execute();
        return $this;
    }

    public function afterCommandExecution(CommandQueueInterface $commandQueue): ?CommandQueueStateInterface
    {
        return $this->stopUponExhaustingQueue && $commandQueue->isQueueEmpty()
            ? null
            : $this;
    }
}
