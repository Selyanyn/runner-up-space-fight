<?php

namespace Hproject\Game\GameObject;

use Hproject\Game\Game\GameObjectInterface;
use Hproject\Infrastructure\Math\FlatVector;

/**
 * Класс снаряда; перемещается, но топлива не требует
 */
class Projectile implements MoveableAndVelocityChangeable, Rotateable, GameObjectInterface
{
    protected int $damage;

    public function __construct(
        private readonly int $id,
        private FlatVector $location,
        private FlatVector $velocity,
        int $damage,
    ) {
        $this->setDamage($damage);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocation(): FlatVector
    {
        return $this->location;
    }

    public function setLocation(FlatVector $location): void
    {
        $this->location = $location;
    }

    public function getVelocity(): FlatVector
    {
        return $this->velocity;
    }

    public function setVelocity(FlatVector $velocity): void
    {
        $this->velocity = $velocity;
    }

    public function getDamage(): int
    {
        return $this->damage;
    }

    public function setDamage(int $damage): void
    {
        if ($damage < 0) {
            throw new \InvalidArgumentException('Урон от снаряда не может быть меньше 0');
        }

        $this->damage = $damage;
    }

    public function finish(): void
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonAgentResponse(): array
    {
        return [
            'location' => [$this->location->x, $this->location->y],
            'velocity' => [$this->velocity->x, $this->velocity->y],
        ];
    }
}
