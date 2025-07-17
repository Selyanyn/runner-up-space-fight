<?php

declare(strict_types=1);

namespace Hproject\Tests;

use Hproject\Game\Command\ChangeVelocityCommand;
use Hproject\Game\Command\MoveCommand;
use Hproject\Game\GameObject\Spaceship;
use Hproject\Infrastructure\Math\FlatVector;
use Hproject\Infrastructure\Queue\BeginQueueProcessingCommand;
use Hproject\Infrastructure\Queue\ChangeQueueStateToMoveToCommand;
use Hproject\Infrastructure\Queue\ChangeQueueStateToRunCommand;
use Hproject\Infrastructure\Queue\CommandQueue;
use PHPUnit\Framework\TestCase;

// Пункт 5 - писать не требуется. Так как я перевёл существующую реализацию команды на состояния, с проверкой
// справится CommandQueueTest. Заодно и мягкую остановку проверит.
final class CommandStateTest extends TestCase
{
    public function testChangeQueueStateToMoveToCommand(): void
    {
        $spaceship = new Spaceship(
            new FlatVector(0.0, 0.0),
            new FlatVector(1.0, 0.0),
            10.0,
        );

        $alternativeQueue = new CommandQueue();

        $queue = new CommandQueue();
        $queue->enqueue(new MoveCommand($spaceship));
        $queue->enqueue(new ChangeQueueStateToMoveToCommand($alternativeQueue));
        $queue->enqueue(new MoveCommand($spaceship));

        $startupCommand = new BeginQueueProcessingCommand($queue);
        $startupCommand->execute();

        // Корабль переместился единожды, хотя исполнение очереди по-прежнему активно...
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(1.0, 0.0)));
        $this->assertTrue($queue->isProcessingActive());

        // Запуск альтернативной команды переместит корабль во второй раз - задача туда доехала.
        $alternativeQueue->beginQueueProcessing();
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(2.0, 0.0)));
    }

    public function testChangeQueueStateToRunCommand(): void
    {
        $spaceship = new Spaceship(
            new FlatVector(0.0, 0.0),
            new FlatVector(1.0, 0.0),
            10.0,
        );

        $alternativeQueue = new CommandQueue();

        $queue = new CommandQueue();
        $queue->enqueue(new ChangeQueueStateToMoveToCommand($alternativeQueue));
        $queue->enqueue(new ChangeVelocityCommand($spaceship, 2.0, 0.0));
        $queue->enqueue(new MoveCommand($spaceship));
        $queue->enqueue(new ChangeQueueStateToRunCommand());
        $queue->enqueue(new MoveCommand($spaceship));

        $startupCommand = new BeginQueueProcessingCommand($queue);
        $startupCommand->execute();

        // Корабль переместился единожды, так как вторая очередь неактивна...
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(1.0, 0.0)));
        $this->assertTrue($queue->isProcessingActive());

        // Запуск альтернативной команды переместит корабль во второй раз - в том числе вызвав изменение скорости.
        $alternativeQueue->beginQueueProcessing();
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(3.0, 0.0)));
    }
}
