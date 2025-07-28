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
use Hproject\Infrastructure\Math\FlatVector;
use PHPUnit\Framework\TestCase;

final class InversionOfControlContainerTest extends TestCase
{
    public function testContainerWithNoParams(): void
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
            'register',
            'moveWithDynamicFuelBurn',
            function (Spaceship $spaceship) {
                $velocityModule = $spaceship->getVelocity()->getModule();

                return new MacroCommand([
                    new CheckFuelCommand($spaceship, $velocityModule),
                    new BurnFuelCommand($spaceship, $velocityModule),
                    new MoveCommand($spaceship),
                ]);
            },
        );
        InversionOfControlContainer::resolve(
            'register',
            'moveWithSetFuelBurn',
            function (Spaceship $spaceship, float $fuel) {
                return new MacroCommand([
                    new CheckFuelCommand($spaceship, $fuel),
                    new BurnFuelCommand($spaceship, $fuel),
                    new MoveCommand($spaceship),
                ]);
            },
        );

        // Движение по стратегии А.
        // В PHP нельзя прописать одновременно именованный параметр и параметр с произвольным количеством элементов.
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

        // Движение по стратегии Б
        /** @var CommandInterface $command */
        $command = InversionOfControlContainer::resolve(
            'moveWithDynamicFuelBurn',
            $spaceship,
        );
        $command->execute();
        $this->assertTrue(
            $spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(11.0, 13.0))
        );
        $this->assertEqualsWithDelta(
            $spaceship->getFuel(),
            2.0,
            0.0000001,
        );

        // Движение по стратегии В
        /** @var CommandInterface $command */
        $command = InversionOfControlContainer::resolve(
            'moveWithSetFuelBurn',
            $spaceship,
            1.2,
        );
        $command->execute();
        $this->assertTrue(
            $spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(14.0, 17.0))
        );
        $this->assertEqualsWithDelta(
            $spaceship->getFuel(),
            0.8,
            0.0000001,
        );
    }
}
