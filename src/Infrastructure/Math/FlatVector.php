<?php

namespace Hproject\Infrastructure\Math;

/**
 * Математическая реализация двухмерного вектора.
 */
final readonly class FlatVector
{
    public function __construct(
        public float $x,
        public float $y,
    ) {
    }

    public function addVector(FlatVector $vector): FlatVector
    {
        return new FlatVector(
            $this->x + $vector->x,
            $this->y + $vector->y,
        );
    }

    public function rotateCounterclockwise(float $angle): FlatVector
    {
        return new FlatVector(
            cos($angle) * $this->x - sin($angle) * $this->y,
            sin($angle) * $this->x + cos($angle) * $this->y,
        );
    }

    public function getModule(): float
    {
        return sqrt($this->x * $this->x + $this->y * $this->y);
    }

    public function isEqualWithEpsilon(FlatVector $vector): bool
    {
        return EpsilonCompare::isEqual($this->x, $vector->x) && EpsilonCompare::isEqual($this->y, $vector->y);
    }
}
