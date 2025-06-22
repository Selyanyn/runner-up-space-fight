<?php

use Hproject\BurnFuelCommand;
use Hproject\CommandException;
use Hproject\CheckFuelCommand;
use Hproject\FlatVector;
use Hproject\IoC\InversionOfControlContainer;
use Hproject\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\IoC\IoCScopeException;
use Hproject\MacroCommand;
use Hproject\Moveable;
use Hproject\MoveCommand;
use Hproject\Queue\BeginQueueProcessingCommand;
use Hproject\Queue\CommandQueue;
use Hproject\Queue\HardStopQueueProcessingCommand;
use Hproject\Queue\SoftStopQueueProcessingCommand;
use Hproject\Spaceship;
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