<?php

namespace Hproject;

/**
 * Для исполнения поворота у объекта должна иметься возможность сменить скорость.
 */
interface Rotateable extends PresentOnFieldInterface
{
    public function setVelocity(FlatVector $vector): void;
}