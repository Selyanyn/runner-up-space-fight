<?php

use Hproject\CommandExceptionHandler;
use Hproject\CommandQueue;
use Hproject\DivisionCommand;
use Hproject\EnqueueCommandCommand;
use Hproject\Logger;
use Hproject\WriteToLogCommand;
use PHPUnit\Framework\TestCase;

/**
 * По ходу реализации задачи я столкнулся с двумя проблемами:
 * - Мне не удавалось отличить друг от друга 1-ую и 2-ую итерации основной исполнительной команды (DivisionCommand).
 * Менять её каким-то образом было бы ужасно. Я решил добавить количество повторов к исполнительному циклу CommandQueue.
 * Мне кажется, это приемлемое решение: в циклах команд часто указывается подобная метаинформация. Это же позволило мне
 * сократить количество команд-повторителей до одной, универсальной.
 * - Я не мог проверить количество вызовов методов, не обернув классы в моки; но это бы потребовало значительных усилий (
 * и я не уверен, что вовсе получилось бы сохранить при этом функциональность классов). Решил сверять итоговое состояние лога.
 */
final class CommandQueueTest extends TestCase
{
    public function testQueue()
    {
        file_put_contents('test.log', "");
        $logger = new Logger('test.log');

        $handler = new CommandExceptionHandler();
        $queue = new CommandQueue($handler);
        $queue->handler->register(
            DivisionCommand::class,
            RuntimeException::class,
            function (DivisionCommand $command, RuntimeException $exception, int $tries) use ($queue, $logger) {
                if ($tries >= 1) {
                    return new EnqueueCommandCommand($queue, new WriteToLogCommand($exception, $logger), $logger, 0);
                } else {
                    return new EnqueueCommandCommand($queue, $command, $logger, $tries + 1);
                }
            }
        );
        $queue->enqueue(new DivisionCommand(1, 0));

        $queue->executeStack();

        $this->assertEquals(
            file_get_contents('test.log'),
            'В очередь установлена команда Hproject\DivisionCommand, повторов: 1' . PHP_EOL .
            'В очередь установлена команда Hproject\WriteToLogCommand, повторов: 0' . PHP_EOL .
            'Невозможно произвести деление на 0' . PHP_EOL
        );
    }
}