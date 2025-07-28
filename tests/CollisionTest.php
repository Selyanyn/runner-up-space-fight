<?php

declare(strict_types=1);

namespace Hproject\Tests;

use Hproject\Game\Command\MoveCommand;
use Hproject\Game\GameObject\MockCheckCollisionCommand;
use Hproject\Game\GameObject\Spaceship;
use Hproject\Game\Neighbourhood\CollisionChecker;
use Hproject\Game\Neighbourhood\NeighbourhoodSystem;
use Hproject\Infrastructure\Math\FlatVector;
use Hproject\Infrastructure\Queue\CommandQueue;
use PHPUnit\Framework\TestCase;

final class CollisionTest extends TestCase
{
    public function testCollision(): void
    {
        $gameObjectA = new Spaceship(
            id: 1,
            location: new FlatVector(1, 1),
            velocity: new FlatVector(2, 2),
            fuel: 10.0
        );
        $gameObjectB = new Spaceship(
            id: 2,
            location: new FlatVector(5, 5),
            velocity: new FlatVector(0, 0),
            fuel: 10.0
        );

        $commandQueue = new CommandQueue();
        $commandQueue->beginQueueProcessing();
        $originalSystem = new NeighbourhoodSystem(new FlatVector(0, 0), 2);
        $systemWithOffset = new NeighbourhoodSystem(new FlatVector(1, 1), 2);
        $collisionChecker = new CollisionChecker($originalSystem, $systemWithOffset, $commandQueue);

        $collisionChecker->checkCollision($gameObjectA);
        $collisionChecker->checkCollision($gameObjectB);

        $commandQueue->enqueue(new MoveCommand($gameObjectA));
        $collisionChecker->checkCollision($gameObjectA);

        $this->assertEquals(0, MockCheckCollisionCommand::getMockCollisionCounter());
        $commandQueue->enqueue(new MoveCommand($gameObjectA));
        $collisionChecker->checkCollision($gameObjectA);
        // Два столкновения - объекты пересекаются в двух системах окрестностей: (4,4) радиус 2 и (6,6) радиус 2
        $this->assertEquals(2, MockCheckCollisionCommand::getMockCollisionCounter());
    }
}
