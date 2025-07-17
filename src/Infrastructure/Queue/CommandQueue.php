<?php

namespace Hproject\Infrastructure\Queue;

use Hproject\Infrastructure\Command\CommandInterface;

final class CommandQueue implements CommandQueueInterface
{
    /**
     * @var list<CommandInterface>
     */
    private array $commands = [];

    private ?CommandQueueStateInterface $state;

    public function __construct()
    {
        $this->state = null;
    }

    /**
     * Для симуляции параллельного выполнения команды исполняем поток сразу же,
     * если очередь активна.
     */
    public function enqueue(CommandInterface $command): void
    {
        $this->commands[] = $command;
        $this->processQueue();
    }

    /**
     * Запускает процесс разборки команд.
     */
    public function beginQueueProcessing(): void
    {
        $this->state = new RunCommandQueueState();
        $this->processQueue();
    }

    /**
     * Останавливает процесс разборки команд.
     */
    public function stopQueueProcessing(): void
    {
        $this->state = null;
    }

    public function isProcessingActive(): bool
    {
        return $this->state !== null;
    }

    public function isQueueEmpty(): bool
    {
        return 0 === count($this->commands);
    }

    /**
     * Исполняет очередь команд.
     *
     * Останавливается немедленно, если очередь неактивна.
     */
    private function processQueue(): void
    {
        foreach ($this->commands as $key => $command) {
            if ($this->state !== null) {
                unset($this->commands[$key]);
                try {
                    $this->state = $this->state->execute($command);
                    $this->state = $this->state?->afterCommandExecution($this);
                } catch (\Throwable $e) {
                    // Ошибку можно было бы залогировать
                }
            } else {
                return;
            }
        }
    }
}
