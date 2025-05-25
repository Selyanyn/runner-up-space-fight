<?php

namespace Hproject;

final class CommandQueue
{
    private array $commandQueue = [];

    public function __construct(
        public CommandExceptionHandler $handler
    )  {
    }

    public function enqueue(CommandInterface $command, int $tries = 0)
    {
        $this->commandQueue[] = [
            'command' => $command,
            'tries' => $tries,
        ];
    }

    public function executeStack(): void
    {
        do {
            $command = array_shift($this->commandQueue);
            if ($command === null) {
                break;
            }
            try {
                $command['command']->execute();
            } catch (\Throwable $e) {
                $this->handler->handle($command['command'], $e, $command['tries'])->execute();
            }
        } while ($command !== null);
    }
}