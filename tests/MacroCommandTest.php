<?php

use Hproject\FlatVector;
use Hproject\BurnFuelCommand;
use Hproject\ChangeVelocityCommand;
use Hproject\CheckFuelCommand;
use Hproject\CommandException;
use Hproject\MacroCommand;
use Hproject\MoveCommand;
use Hproject\RotateCounterclockwiseCommand;
use Hproject\Spaceship;
use PHPUnit\Framework\TestCase;

final class MacroCommandTest extends TestCase
{
    public function testMoveWithBurn()
    {
        $spaceship = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );

        $moveWithBurnCommand = new MacroCommand([
            new CheckFuelCommand($spaceship, 3.0),
            new BurnFuelCommand($spaceship, 3.0),
            new MoveCommand($spaceship),
        ]);

        $moveWithBurnCommand->execute();
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(8.0, 6.0)));
        $this->assertEqualsWithDelta($spaceship->getFuel(), 7.0, 0.0000001);

        $moveWithBurnCommand->execute();
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(11.0, 7.0)));
        $this->assertEqualsWithDelta($spaceship->getFuel(), 4.0, 0.0000001);

        $moveWithBurnCommand->execute();
        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(14.0, 8.0)));
        $this->assertEqualsWithDelta($spaceship->getFuel(), 1.0, 0.0000001);
        
        $this->expectException(CommandException::class);
        $moveWithBurnCommand->execute();
    }

    public function testRotationWithVelocityChangeCommand()
    {
        $spaceship = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(10.0, 40.0),
            10.0,
        );

        $rotationWithVelocityChangeCommand = new MacroCommand([
            new RotateCounterclockwiseCommand($spaceship, 90),
            new ChangeVelocityCommand($spaceship, 0.5, 0.5),
        ]);

        $rotationWithVelocityChangeCommand->execute();
        $this->assertTrue($spaceship->getVelocity()->isEqualWithEpsilon(new FlatVector(-20.0, 5.0)));

        $rotationWithVelocityChangeCommand->execute();
        $this->assertTrue($spaceship->getVelocity()->isEqualWithEpsilon(new FlatVector(-2.5, -10.0)));
    }
}