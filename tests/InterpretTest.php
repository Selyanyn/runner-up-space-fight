<?php

declare(strict_types=1);

namespace Hproject\Tests;

use Hproject\Game\Command\MoveCommand;
use Hproject\Game\Expression\InitIoCContainerInterpret;
use Hproject\Game\Expression\InterpretCommand;
use Hproject\Game\GameObject\Spaceship;
use Hproject\Infrastructure\Interpreter\Context;
use Hproject\Infrastructure\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\Infrastructure\Math\FlatVector;
use PHPUnit\Framework\TestCase;

final class InterpretTest extends TestCase
{
    public function testBasicInterpret(): void
    {
        InitIoCContainerActionStrategyRegistry::init();
        InitIoCContainerInterpret::init();

        $spaceship = new Spaceship(
            1,
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 4.0),
            10.0,
        );

        $context = new Context();
        $context->set('game_object_'.$spaceship->getId(), $spaceship);

        $command = new InterpretCommand(
            'move',
            $context,
            [
                $spaceship->getId(),
                new FlatVector(7.0, 3.0),
            ]
        );
        $command->execute();

        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(12.0, 8.0)));
        $this->assertTrue($spaceship->getVelocity()->isEqualWithEpsilon(new FlatVector(7.0, 3.0)));

        $command = new InterpretCommand(
            'stop',
            $context,
            [
                $spaceship->getId(),
            ]
        );
        $command->execute();
        (new MoveCommand($spaceship))->execute();

        $this->assertTrue($spaceship->getLocation()->isEqualWithEpsilon(new FlatVector(12.0, 8.0)));
        $this->assertTrue($spaceship->getVelocity()->isEqualWithEpsilon(new FlatVector(0.0, 0.0)));
    }
}
