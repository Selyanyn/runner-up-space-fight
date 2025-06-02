<?php

namespace Hproject;

/**
 * Наличие топлива подразумевает возможность его траты, поэтому делить интерфейс на наличие \ расход,
 * подобно делению на "движимый" \ "поворачиваемый" я не стал.
 */
interface HasFuel
{
    public function getFuel(): float;

    public function burnFuel(float $burntFuel): void;
}