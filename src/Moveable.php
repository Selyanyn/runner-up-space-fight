<?php

namespace Hproject;

/**
 * Для исполнения движения у объекта должна иметься возможность сменить координаты.
 */
interface Moveable extends PresentOnFieldInterface
{
    public function setLocation(FlatVector $vector): void;

    // Добавлено только ради п. 3 из задания. Структура реализованного адаптера позволяет обработать любые методы интерфейса.
    public function finish(): void;
}