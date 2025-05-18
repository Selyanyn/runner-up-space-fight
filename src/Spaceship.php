<?php

namespace Hproject;

/**
 * Простйешая реализация интерфейсов Moveable и Rotateable
 */
final class Spaceship implements Moveable, Rotateable
{
    public function __construct(
        private FlatVector $location,
        private FlatVector $velocity,
    ) {}
    
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
}