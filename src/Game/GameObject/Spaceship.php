<?php

namespace Hproject\Game\GameObject;

use Hproject\Game\Game\GameObjectInterface;
use Hproject\Infrastructure\Math\FlatVector;
use Hproject\Infrastructure\Math\EpsilonCompare;

/**
 * Простйешая реализация интерфейсов Moveable и Rotateable.
 */
final class Spaceship implements Moveable, Rotateable, HasFuel, VelocityChangeable, GameObjectInterface
{
    public function __construct(
        private FlatVector $location,
        private FlatVector $velocity,
        private float $fuel,
    ) {
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

    public function getFuel(): float
    {
        return $this->fuel;
    }

    public function burnFuel(float $burntFuel): void
    {
        if (EpsilonCompare::greaterThan($burntFuel, $this->fuel)) {
            throw new \InvalidArgumentException('Нельзя потратить больше топлива, чем есть в корабле!');
        }

        $this->fuel -= $burntFuel;
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
            'fuel' => $this->fuel,
        ];
    }
}
