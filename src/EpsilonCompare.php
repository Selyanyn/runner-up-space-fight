<?php

namespace Hproject;

/**
 * Простейшая реализация сравнения с epsilon.
 */
final readonly class EpsilonCompare
{
    public static function greaterThan(float $a, float $b, $epsilon = 0.000001)
    {
        return $a - $b > $epsilon;
    }

    public static function isEqual(float $a, float $b, $epsilon = 0.000001)
    {
        return abs($a - $b) < $epsilon;
    }
}