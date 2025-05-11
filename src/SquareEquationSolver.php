<?php

declare(strict_types=1);

namespace Hproject;

use InvalidArgumentException;

final readonly class SquareEquationSolver
{
    private static function epsilonEqual(float $a, float $b, float $epsilon = 0.000001): bool
    {
        return ($a - $b) < $epsilon;
    }

    private static function epsilonGreaterThan(float $a, float $b, float $epsilon = 0.000001): bool
    {
        return $a - $epsilon > $b;
    }

    /**
    * @return list<float>
    */
    public static function solve(float $a, float $b, float $c): array
    {
        if (self::epsilonEqual($a, 0.0)) {
            throw new InvalidArgumentException("Параметр а не может быть равен 0");
        }

        $discriminant = $b * $b - 4 * $a * $c;
        if (self::epsilonGreaterThan(0.0, $discriminant)) {
            return [];
        }

        if (self::epsilonEqual($discriminant, 0.0)) {
            return [- $b / (2 * $a)];
        }

        return [
            (- $b - sqrt($discriminant)) / (2 * $a),
            (- $b + sqrt($discriminant)) / (2 * $a),
        ];
    }
}