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
use Hproject\Spaceship;
use PHPUnit\Framework\TestCase;

final class AdapterTest extends TestCase
{
    // Корректно получает родительскую зависимость
    public function testGetParentScopeDependencySuccess()
    {
        InitIoCContainerActionStrategyRegistry::init();

        $rawSpaceshipClass = new class {
            public float $locationX = 10.0;
            public float $locationY = 20.0;
            public float $velocityX = 2.0;
            public float $velocityY = -3.0;
            public float $fuel = 12.0;
        };
        $rawSpaceship = new $rawSpaceshipClass();

        InversionOfControlContainer::resolve(
            'register',
            "Hproject\Moveable::getLocation",
            fn ($spaceship) => new FlatVector($spaceship->locationX, $spaceship->locationY),
        );

        InversionOfControlContainer::resolve(
            'register',
            "Hproject\Moveable::getVelocity",
            fn ($spaceship) => new FlatVector($spaceship->velocityX, $spaceship->velocityY),
        );

        InversionOfControlContainer::resolve(
            'register',
            "Hproject\Moveable::setLocation",
            function ($spaceship, FlatVector $location) {
                $spaceship->locationX = $location->x;
                $spaceship->locationY = $location->y;
            },
        );

        InversionOfControlContainer::resolve(
            'register',
            "Hproject\Moveable::finish",
            function ($spaceship) {
                $spaceship->fuel = 0.0;
            },
        );

        $adapter = InversionOfControlContainer::resolve(
            'adapter',
            Moveable::class,
            $rawSpaceship,
        );

        $this->assertTrue(
            $adapter->getLocation()->isEqualWithEpsilon(new FlatVector(10.0, 20.0))
        );
        $this->assertTrue(
            $adapter->getVelocity()->isEqualWithEpsilon(new FlatVector(2.0, -3.0))
        );

        $adapter->setLocation(new FlatVector(-2.0, -13.0));
        $this->assertTrue(
            $adapter->getLocation()->isEqualWithEpsilon(new FlatVector(-2.0, -13.0))
        );

        $adapter->finish();
        $this->assertEqualsWithDelta($rawSpaceship->fuel, 0.0, 0.0000001);
    }
}