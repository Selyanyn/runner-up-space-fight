<?php

namespace Hproject\Tests;

use Hproject\Game\Command\BurnFuelCommand;
use Hproject\Game\Command\CheckFuelCommand;
use Hproject\Game\Game\Game;
use Hproject\Game\Game\GameState;
use Hproject\Game\GameObject\Spaceship;
use Hproject\Infrastructure\Command\MacroCommand;
use Hproject\Infrastructure\Interpretate\InterpretateCommand;
use Hproject\Infrastructure\Interpretate\InterpretateCommandStrategyRegistry;
use Hproject\Infrastructure\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\Infrastructure\IoC\InversionOfControlContainer;
use Hproject\Infrastructure\Math\FlatVector;
use Hproject\Infrastructure\Queue\CommandQueue;
use PHPUnit\Framework\TestCase;

final class InterpretateCommandTest extends TestCase
{
    // Корректно интерпретирует команды
    public function testBasic(): void
    {
        InitIoCContainerActionStrategyRegistry::init();
        InterpretateCommandStrategyRegistry::init();

        $gameObject = new Spaceship(
            1,
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );
        $game = new Game(
            1,
            new CommandQueue(),
            new GameState([
                1 => $gameObject,
            ]),
            [1, 2],
        );

        $interpretateCommand = new InterpretateCommand(
            $game,
            $gameObject,
            'move',
            [3.0],
        );
        $interpretateCommand->execute();

        $this->assertTrue($gameObject->getLocation()->isEqualWithEpsilon(new FlatVector(8.0, 6.0)));
        $this->assertTrue($gameObject->getVelocity()->isEqualWithEpsilon(new FlatVector(3.0, 1.0)));
        $this->assertEqualsWithDelta($gameObject->getFuel(), 7.0, 0.0000001);

        // Интерпретатор пользуется очередью команд нашей игры. При её отключении исполнение очередной команды
        // не должно происходить.
        $game->commandQueue->stopQueueProcessing();
        $interpretateCommand->execute();
        $this->assertTrue($gameObject->getLocation()->isEqualWithEpsilon(new FlatVector(8.0, 6.0)));
        $this->assertTrue($gameObject->getVelocity()->isEqualWithEpsilon(new FlatVector(3.0, 1.0)));
        $this->assertEqualsWithDelta($gameObject->getFuel(), 7.0, 0.0000001);
    }

    // Интерпретатор может разобрать созданную и внедрённую на ходу произвольную команду
    public function testNewInterpretation(): void
    {
        InitIoCContainerActionStrategyRegistry::init();
        InterpretateCommandStrategyRegistry::init();

        InversionOfControlContainer::resolve(
            'register',
            'interpretate.burnHalfFuel',
            fn ($spaceship) => new MacroCommand([
                new CheckFuelCommand($spaceship, 1),
                new BurnFuelCommand($spaceship, ceil($spaceship->getFuel() / 2)),
            ]),
        );

        $gameObject = new Spaceship(
            1,
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );
        $game = new Game(
            1,
            new CommandQueue(),
            new GameState([
                1 => $gameObject,
            ]),
            [1, 2],
        );

        $interpretateCommand = new InterpretateCommand(
            $game,
            $gameObject,
            'burnHalfFuel',
            [],
        );
        $interpretateCommand->execute();

        $this->assertTrue($gameObject->getLocation()->isEqualWithEpsilon(new FlatVector(5.0, 5.0)));
        $this->assertTrue($gameObject->getVelocity()->isEqualWithEpsilon(new FlatVector(3.0, 1.0)));
        $this->assertEqualsWithDelta($gameObject->getFuel(), 5.0, 0.0000001);
    }
}
