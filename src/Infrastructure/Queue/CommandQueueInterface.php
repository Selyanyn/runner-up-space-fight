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

    /**
     * Добавляет событие, активируемое перед активацией команды.
     *
     * Я не стал перекладывать пример из урока в свою реализацию, так как не согласен с ним.
     * Он потенциально позволяет полностью переписать внутренний цикл исполнения команд, что
     * нарушает приницпы SOLID. Механизм событий же позволяет использовать лишь то, что
     * было изначально предусмотрено разработчиком очереди в интерфейсе очереди команд.
     */
    public function addBeforeCommandProcessingEvent(callable $event): void;
}
