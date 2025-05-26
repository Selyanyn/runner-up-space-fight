<?php

use Hproject\CheckFuelCommand;
use Hproject\CommandException;
use Hproject\FlatVector;
use Hproject\Spaceship;
use PHPUnit\Framework\TestCase;

final class CheckFuelCommandTest extends TestCase
{
    public function testCheckFuelCommandSuccess()
    {
        $this->expectNotToPerformAssertions();
        $spaceShip = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );
        $command = new CheckFuelCommand($spaceShip, 5.0);
        $command->execute();
    }

    public function testCheckFuelCommandThrowsExceptionOnFailedCheck()
    {
        $this->expectException(CommandException::class);
        $spaceShip = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );
        $command = new CheckFuelCommand($spaceShip, 15.0);
        $command->execute();
    }
}