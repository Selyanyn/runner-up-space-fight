<?php

namespace Hproject;

/**
 * Интерфейс смены скорости.
 *
 * Не пересекается с Rotateable, хотя и ожидает команду с той же сигнатурой.
 */
interface VelocityChangeable extends PresentOnFieldInterface
{
    public function setVelocity(FlatVector $vector): void;
}