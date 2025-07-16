<?php

namespace Hproject\Infrastructure\Queue;

use Hproject\Infrastructure\Command\CommandInterface;

final class CommandQueue implements CommandQueueInterface
{
    private bool $isProcessingActive = false;

    /**
     * @var list<CommandInterface>
     */
    private array $commands = [];

    /**
     * @var list<callable>
     */
    private array $beforeCommanProcessingEvents = [];

    public function __construct()
    {
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
        $this->isProcessingActive = true;
        $this->processQueue();
    }

    /**
     * Останавливает процесс разборки команд.
     */
    public function stopQueueProcessing(): void
    {
        $this->isProcessingActive = false;
    }

    public function isProcessingActive(): bool
    {
        return $this->isProcessingActive;
    }

    public function isQueueEmpty(): bool
    {
        return 0 === count($this->commands);
    }

    public function addBeforeCommandProcessingEvent(callable $event): void
    {
        $this->beforeCommanProcessingEvents[] = $event;
    }

    /**
     * Исполняет очередь команд.
     *
     * Останавливается немедленно, если очередь неактивна.
     */
    private function processQueue(): void
    {
        foreach ($this->commands as $key => $command) {
            if ($this->isProcessingActive) {
                unset($this->commands[$key]);
                try {
                    foreach ($this->beforeCommanProcessingEvents as $event) {
                        $event();
                    }
                    $command->execute();
                } catch (\Throwable $e) {
                    // Ошибку можно было бы залогировать
                }
            } else {
                return;
            }
        }
    }
}
