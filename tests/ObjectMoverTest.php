<?php

use Hproject\FlatVector;
use Hproject\ObjectMover;
use Hproject\Spaceship;
use PHPUnit\Framework\TestCase;

final class ObjectMoverTest extends TestCase
{
    public function testBasicMovement()
    {
        $spaceship = new Spaceship(
            location: new FlatVector(12, 5),
            velocity: new FlatVector(-7, 3),
        );

        ObjectMover::moveObject($spaceship);

        $this->assertEqualsWithDelta(
            $spaceship->getLocation()->x,
            5,
            0.000001,
            'Неверное положение объекта по x',
        );

        $this->assertEqualsWithDelta(
            $spaceship->getLocation()->y,
            8,
            0.000001,
            'Неверное положение объекта по y',
        );
    }

    /**
     * Все три случая из задания покрываются одним тестом, так как при передаче любого
     * неисполняющего интерфейс объекта будет выброшена ошибка вида TypeError:
     * - Argument #1 ($object) must be of type Hproject\Moveable, <класс> given
     */
    public function testFailOnMovingNonMoveableObject()
    {
        $this->expectException(TypeError::class);

        ObjectMover::moveObject(new \stdClass());
    }
}