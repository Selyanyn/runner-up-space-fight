<?php

use Hproject\BurnFuelCommand;
use Hproject\CommandException;
use Hproject\CheckFuelCommand;
use Hproject\FlatVector;
use Hproject\Game\Game;
use Hproject\Game\GameState;
use Hproject\Interpretate\InterpretateCommand;
use Hproject\Interpretate\InterpretateCommandStrategyRegistry;
use Hproject\IoC\InversionOfControlContainer;
use Hproject\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\IoC\IoCScopeException;
use Hproject\Queue\CommandQueue;
use Hproject\MacroCommand;
use Hproject\Moveable;
use Hproject\MoveCommand;
use Hproject\Spaceship;
use PHPUnit\Framework\TestCase;

final class InterpretateCommandTest extends TestCase
{
    // Корректно интерпретирует команды
    public function testBasic()
    {
        InitIoCContainerActionStrategyRegistry::init();
        InterpretateCommandStrategyRegistry::init();

        $gameObject = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );
        $game = new Game(
            new CommandQueue(),
            new GameState([
                1 => $gameObject,
            ])
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
    public function testNewInterpretation()
    {
        InitIoCContainerActionStrategyRegistry::init();
        InterpretateCommandStrategyRegistry::init();

        InversionOfControlContainer::resolve(
            'register',
            "interpretate.burnHalfFuel",
            fn ($spaceship) => new MacroCommand([
                new CheckFuelCommand($spaceship, 1),
                new BurnFuelCommand($spaceship, ceil($spaceship->getFuel() / 2)),
            ]),
        );

        $gameObject = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );
        $game = new Game(
            new CommandQueue(),
            new GameState([
                1 => $gameObject,
            ])
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