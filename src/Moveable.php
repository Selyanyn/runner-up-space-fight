<?php

namespace Hproject;

/**
 * Для исполнения движения у объекта должна иметься возможность сменить координаты.
 */
interface Moveable extends PresentOnFieldInterface
{
    public function setLocation(FlatVector $vector): void;
}