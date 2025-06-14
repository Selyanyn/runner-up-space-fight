<?php

use Hproject\BurnFuelCommand;
use Hproject\CommandException;
use Hproject\CheckFuelCommand;
use Hproject\FlatVector;
use Hproject\IoC\InversionOfControlContainer;
use Hproject\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\IoC\IoCScopeException;
use Hproject\MacroCommand;
use Hproject\MoveCommand;
use Hproject\Spaceship;
use PHPUnit\Framework\TestCase;

final class IoCScopeTest extends TestCase
{
    // Корректно получает родительскую зависимость
    public function testGetParentScopeDependencySuccess()
    {
        InitIoCContainerActionStrategyRegistry::init();

        $spaceship = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 4.0),
            10.0,
        );

        InversionOfControlContainer::resolve(
            'register',
            "moveWithConstantFuelBurn",
            fn (Spaceship $spaceship) => new MacroCommand([
                new CheckFuelCommand($spaceship, 3.0),
                new BurnFuelCommand($spaceship, 3.0),
                new MoveCommand($spaceship),
            ]),
        );
        InversionOfControlContainer::resolve(
            'addScope',
            "sub",
        );
        InversionOfControlContainer::resolve(
            'goToChildScope',
            "sub",
        );

        $command = InversionOfControlContainer::resolve(
            "moveWithConstantFuelBurn",
            $spaceship,
        );
        $command->execute();
        $this->assertTrue(
            $spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(8.0, 9.0))
        );
        $this->assertEqualsWithDelta(
            $spaceship->getFuel(),
            7.0,
            0.0000001,
        );
    }

    // Родитель не получает зависимость потомка
    public function testGetChildScopeDependencyFail()
    {
        InitIoCContainerActionStrategyRegistry::init();

        $spaceship = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 4.0),
            10.0,
        );

        InversionOfControlContainer::resolve(
            'addScope',
            "sub",
        );
        InversionOfControlContainer::resolve(
            'goToChildScope',
            "sub",
        );
        InversionOfControlContainer::resolve(
            'register',
            "moveWithConstantFuelBurn",
            fn (Spaceship $spaceship) => new MacroCommand([
                new CheckFuelCommand($spaceship, 3.0),
                new BurnFuelCommand($spaceship, 3.0),
                new MoveCommand($spaceship),
            ]),
        );
        InversionOfControlContainer::resolve(
            'goToParentScope',
        );

        $this->expectException(IoCScopeException::class);
        $command = InversionOfControlContainer::resolve(
            "moveWithConstantFuelBurn",
            $spaceship,
        );
    }

    // Можно зарегистрировать две зависимости в разных скоупах
    public function testRegisterDependenciesInSiblingScopesSuccess()
    {
        InitIoCContainerActionStrategyRegistry::init();

        $spaceship = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 4.0),
            10.0,
        );

        InversionOfControlContainer::resolve(
            'addScope',
            "sub2",
        );
        InversionOfControlContainer::resolve(
            'goToChildScope',
            "sub2",
        );
        InversionOfControlContainer::resolve(
            'register',
            "moveWithConstantFuelBurn",
            fn (Spaceship $spaceship) => new MacroCommand([
                new CheckFuelCommand($spaceship, 3.0),
                new BurnFuelCommand($spaceship, 3.0),
                new MoveCommand($spaceship),
            ]),
        );
        InversionOfControlContainer::resolve(
            'goToParentScope',
        );
        InversionOfControlContainer::resolve(
            'addScope',
            "sub",
        );
        InversionOfControlContainer::resolve(
            'goToChildScope',
            "sub",
        );
        InversionOfControlContainer::resolve(
            'register',
            "moveWithConstantFuelBurn",
            fn (Spaceship $spaceship) => new MacroCommand([
                new CheckFuelCommand($spaceship, 1.0),
                new BurnFuelCommand($spaceship, 1.0),
                new MoveCommand($spaceship),
            ]),
        );

        $command = InversionOfControlContainer::resolve(
            "moveWithConstantFuelBurn",
            $spaceship,
        );
        $command->execute();
        $this->assertTrue(
            $spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(8.0, 9.0))
        );
        $this->assertEqualsWithDelta(
            $spaceship->getFuel(),
            9.0,
            0.0000001,
        );

        InversionOfControlContainer::resolve(
            'goToParentScope',
        );
        InversionOfControlContainer::resolve(
            'goToChildScope',
            "sub2",
        );

        $command = InversionOfControlContainer::resolve(
            "moveWithConstantFuelBurn",
            $spaceship,
        );
        $command->execute();
        $this->assertTrue(
            $spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(11.0, 13.0))
        );
        $this->assertEqualsWithDelta(
            $spaceship->getFuel(),
            6.0,
            0.0000001,
        );
    }
}