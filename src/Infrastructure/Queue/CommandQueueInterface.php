<?php

namespace Hproject\Infrastructure\Queue;

use Hproject\Infrastructure\Command\CommandInterface;

/**
 * PHP не обладает многопоточностью. В реальном проекте многопоточность, как правило, достигается
 * за счёт связки из запуска нескольких PHP-процессов и брокера сообщений.
 * Этого нельзя добиться здесь - у нас один процесс для гитхаб-тестов; поэтому аспект многопоточности
 * реализован не будет.
 */
interface CommandQueueInterface
{
    /**
     * Добавляет команду к очереди.
     */
    public function enqueue(CommandInterface $command): void;

    /**
     * Запускает процесс разборки команд.
     */
    public function beginQueueProcessing(): void;

    /**
     * Останавливает процесс разборки команд.
     */
    public function stopQueueProcessing(): void;

    /**
     * Активен ди процесс разборки команд.
     */
    public function isProcessingActive(): bool;

    /**
     * Содержит ли очередь неразобранные задачи.
     */
    public function isQueueEmpty(): bool;
}
