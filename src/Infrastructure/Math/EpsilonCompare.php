<?php

namespace Hproject\Infrastructure\Math;

/**
 * Простейшая реализация сравнения с epsilon.
 */
final readonly class EpsilonCompare
{
    public static function greaterThan(float $a, float $b, float $epsilon = 0.000001): bool
    {
        return $a - $b > $epsilon;
    }

    public static function isEqual(float $a, float $b, float $epsilon = 0.000001): bool
    {
        return abs($a - $b) < $epsilon;
    }
}
