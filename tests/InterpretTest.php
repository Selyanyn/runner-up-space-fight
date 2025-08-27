<?php

declare(strict_types=1);

namespace Hproject\Tests;

use Hproject\Game\Command\MoveCommand;
use Hproject\Game\Expression\InitIoCContainerInterpret;
use Hproject\Game\Expression\InterpretCommand;
use Hproject\Game\Game\Game;
use Hproject\Game\Game\GameState;
use Hproject\Game\GameObject\Spaceship;
use Hproject\Infrastructure\Interpreter\Context;
use Hproject\Infrastructure\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\Infrastructure\Math\FlatVector;
use Hproject\Infrastructure\Queue\CommandQueue;
use PHPUnit\Framework\TestCase;

final class InterpretTest extends TestCase
{
    public function testBasicInterpret(): void
    {
        InitIoCContainerActionStrategyRegistry::init();
        InitIoCContainerInterpret::init();

        $userId = 1;
        $spaceship = new Spaceship(
            1,
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 4.0),
            10.0,
        );

        $gameState = new GameState();
        $gameState->addGameObject($spaceship, [$userId]);

        $game = new Game(
            id: 1,
            commandQueue: new CommandQueue(),
            gameState: $gameState,
            playersIds: [$userId],
        );

        $context = new Context();
        $context->set('game', $game);
        $context->set('userId', $userId);

        // Проверка движения
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

        // Проверка остановки
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

        // Проверка выстрела (спаун снаряда)
        $command = new InterpretCommand(
            'shoot',
            $context,
            [
                $spaceship->getId(),
                new FlatVector(-4.0, -2.0),
                6,
            ]
        );
        $command->execute();

        $projectile = $gameState->getGameObject(2, [$userId]);
        $this->assertNotNull($projectile);
        $this->assertTrue($projectile->getLocation()->isEqualWithEpsilon(new FlatVector(8.0, 6.0)));
        $this->assertTrue($projectile->getVelocity()->isEqualWithEpsilon(new FlatVector(-4.0, -2.0)));
    }

    public function testInterpterWithOwnership(): void
    {
        InitIoCContainerActionStrategyRegistry::init();
        InitIoCContainerInterpret::init();

        $spaceship = new Spaceship(
            1,
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 4.0),
            10.0,
        );

        $gameState = new GameState();
        $gameState->addGameObject($spaceship, [1]);

        $game = new Game(
            id: 1,
            commandQueue: new CommandQueue(),
            gameState: $gameState,
            playersIds: [1, 2],
        );

        // Корабль создал первый игрок, но активным в контексте объявлен второй
        $context = new Context();
        $context->set('game', $game);
        $context->set('userId', 2);

        $this->expectException(\InvalidArgumentException::class);

        // Проверка движения
        $command = new InterpretCommand(
            'move',
            $context,
            [
                $spaceship->getId(),
                new FlatVector(7.0, 3.0),
            ]
        );
        $command->execute();
    }
}
