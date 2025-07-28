<?php

namespace Hproject\Tests;

use Hproject\Game\Command\BurnFuelCommand;
use Hproject\Game\Command\CheckFuelCommand;
use Hproject\Game\Command\MoveCommand;
use Hproject\Game\GameObject\Spaceship;
use Hproject\Infrastructure\Command\CommandInterface;
use Hproject\Infrastructure\Command\MacroCommand;
use Hproject\Infrastructure\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\Infrastructure\IoC\InversionOfControlContainer;
use Hproject\Infrastructure\IoC\IoCScopeException;
use Hproject\Infrastructure\Math\FlatVector;
use PHPUnit\Framework\TestCase;

final class IoCScopeTest extends TestCase
{
    // Корректно получает родительскую зависимость
    public function testGetParentScopeDependencySuccess(): void
    {
        InitIoCContainerActionStrategyRegistry::init();

        $spaceship = new Spaceship(
            1,
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 4.0),
            10.0,
        );

        InversionOfControlContainer::resolve(
            'register',
            'moveWithConstantFuelBurn',
            fn (Spaceship $spaceship) => new MacroCommand([
                new CheckFuelCommand($spaceship, 3.0),
                new BurnFuelCommand($spaceship, 3.0),
                new MoveCommand($spaceship),
            ]),
        );
        InversionOfControlContainer::resolve(
            'addScope',
            'sub',
        );
        InversionOfControlContainer::resolve(
            'goToChildScope',
            'sub',
        );

        /** @var CommandInterface $command */
        $command = InversionOfControlContainer::resolve(
            'moveWithConstantFuelBurn',
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
    public function testGetChildScopeDependencyFail(): void
    {
        InitIoCContainerActionStrategyRegistry::init();

        $spaceship = new Spaceship(
            1,
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 4.0),
            10.0,
        );

        InversionOfControlContainer::resolve(
            'addScope',
            'sub',
        );
        InversionOfControlContainer::resolve(
            'goToChildScope',
            'sub',
        );
        InversionOfControlContainer::resolve(
            'register',
            'moveWithConstantFuelBurn',
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
            'moveWithConstantFuelBurn',
            $spaceship,
        );
    }

    // Можно зарегистрировать две зависимости в разных скоупах
    public function testRegisterDependenciesInSiblingScopesSuccess(): void
    {
        InitIoCContainerActionStrategyRegistry::init();

        $spaceship = new Spaceship(
            1,
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 4.0),
            10.0,
        );

        InversionOfControlContainer::resolve(
            'addScope',
            'sub2',
        );
        InversionOfControlContainer::resolve(
            'goToChildScope',
            'sub2',
        );
        InversionOfControlContainer::resolve(
            'register',
            'moveWithConstantFuelBurn',
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
            'sub',
        );
        InversionOfControlContainer::resolve(
            'goToChildScope',
            'sub',
        );
        InversionOfControlContainer::resolve(
            'register',
            'moveWithConstantFuelBurn',
            fn (Spaceship $spaceship) => new MacroCommand([
                new CheckFuelCommand($spaceship, 1.0),
                new BurnFuelCommand($spaceship, 1.0),
                new MoveCommand($spaceship),
            ]),
        );

        /** @var CommandInterface $command */
        $command = InversionOfControlContainer::resolve(
            'moveWithConstantFuelBurn',
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
            'sub2',
        );

        /** @var CommandInterface $command */
        $command = InversionOfControlContainer::resolve(
            'moveWithConstantFuelBurn',
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
