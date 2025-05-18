<?php

use Hproject\FlatVector;
use Hproject\ObjectRotator;
use Hproject\Spaceship;
use PHPUnit\Framework\TestCase;

final class ObjectRotatorTest extends TestCase
{
    public function testBasicRotation()
    {
        $spaceship = new Spaceship(
            location: new FlatVector(12, 5),
            velocity: new FlatVector(-7, 3),
        );

        ObjectRotator::rotateObjectCounterclockwise($spaceship, deg2rad(-90));

        $this->assertEqualsWithDelta(
            $spaceship->getVelocity()->x,
            3,
            0.000001,
            'Неверная скорость объекта по x',
        );

        $this->assertEqualsWithDelta(
            $spaceship->getVelocity()->y,
            7,
            0.000001,
            'Неверная скорость объекта по y',
        );
    }

    /**
     * Все три случая из задания (как и с движением) покрываются одним тестом, так как при передаче любого
     * неисполняющего интерфейс объекта будет выброшена ошибка вида TypeError:
     * - Argument #1 ($object) must be of type Hproject\Rotateable, <класс> given
     */
    public function testFailOnRotatingNonRotateableObject()
    {
        $this->expectException(TypeError::class);

        ObjectRotator::rotateObjectCounterclockwise(new \stdClass(), deg2rad(-90));
    }
}