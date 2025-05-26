<?php

use Hproject\BurnFuelCommand;
use Hproject\CommandException;
use Hproject\FlatVector;
use Hproject\Spaceship;
use PHPUnit\Framework\TestCase;

final class BurnFuelCommandTest extends TestCase
{
    public function testBurnFuelCommandSuccess()
    {
        $spaceShip = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );
        $command = new BurnFuelCommand($spaceShip, 4.0);
        $command->execute();
        $this->assertEqualsWithDelta(
            $spaceShip->getFuel(),
            6.0,
            0.0000001,
        );
    }

    public function testBurnFuelCommandThrowsExceptionOnBurnout()
    {
        $this->expectException(\InvalidArgumentException::class);
        $spaceShip = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );
        $command = new BurnFuelCommand($spaceShip, 15.0);
        $command->execute();
    }
}