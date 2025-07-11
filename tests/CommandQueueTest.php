<?php

use Hproject\Game\Command\BurnFuelCommand;
use Hproject\Infrastructure\Math\FlatVector;
use Hproject\Game\Command\MoveCommand;
use Hproject\Infrastructure\Queue\BeginQueueProcessingCommand;
use Hproject\Infrastructure\Queue\CommandQueue;
use Hproject\Infrastructure\Queue\HardStopQueueProcessingCommand;
use Hproject\Infrastructure\Queue\SoftStopQueueProcessingCommand;
use Hproject\Game\GameObject\Spaceship;
use PHPUnit\Framework\TestCase;

final class CommandQueueTest extends TestCase
{
    public function testCommandProcessing()
    {
        $spaceship = new Spaceship(
            new FlatVector(0.0, 0.0),
            new FlatVector(1.0, 0.0),
            10.0,
        );

        $queue = new CommandQueue();
        $queue->enqueue(new MoveCommand($spaceship));
        $queue->enqueue(new MoveCommand($spaceship));

        $startupCommand = new BeginQueueProcessingCommand($queue);
        $startupCommand->execute();

        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(2.0, 0.0)));
        
        // Проверка того, что очередь потребляет команды (старые команды не активируются снова)
        $queue->enqueue(new MoveCommand($spaceship));
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(3.0, 0.0)));
    }

    public function testCommandProcessingWithErroneousCommand()
    {
        $spaceship = new Spaceship(
            new FlatVector(0.0, 0.0),
            new FlatVector(1.0, 0.0),
            10.0,
        );

        $queue = new CommandQueue();
        $queue->enqueue(new MoveCommand($spaceship));
        // Эта команда бросит исключение
        $queue->enqueue(new BurnFuelCommand($spaceship, 999.00));
        $queue->enqueue(new MoveCommand($spaceship));

        $startupCommand = new BeginQueueProcessingCommand($queue);
        $startupCommand->execute();

        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(2.0, 0.0)));
    }

    public function testHardStop()
    {
        $spaceship = new Spaceship(
            new FlatVector(0.0, 0.0),
            new FlatVector(1.0, 0.0),
            10.0,
        );

        $queue = new CommandQueue();
        $queue->enqueue(new MoveCommand($spaceship));
        $queue->enqueue(new HardStopQueueProcessingCommand($queue));
        $queue->enqueue(new MoveCommand($spaceship));

        $startupCommand = new BeginQueueProcessingCommand($queue);
        $startupCommand->execute();

        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(1.0, 0.0)));
        $this->assertFalse($queue->isProcessingActive());

        // Команды действительно больше не исполняются
        $queue->enqueue(new MoveCommand($spaceship));
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(1.0, 0.0)));
    }

    public function testSoftStop()
    {
        $spaceship = new Spaceship(
            new FlatVector(0.0, 0.0),
            new FlatVector(1.0, 0.0),
            10.0,
        );

        $queue = new CommandQueue();
        $queue->enqueue(new MoveCommand($spaceship));
        $queue->enqueue(new SoftStopQueueProcessingCommand($queue));
        $queue->enqueue(new MoveCommand($spaceship));

        $startupCommand = new BeginQueueProcessingCommand($queue);
        $startupCommand->execute();

        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(2.0, 0.0)));
        $this->assertFalse($queue->isProcessingActive());

        // Команды действительно больше не исполняются
        $queue->enqueue(new MoveCommand($spaceship));
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(2.0, 0.0)));
    }
}