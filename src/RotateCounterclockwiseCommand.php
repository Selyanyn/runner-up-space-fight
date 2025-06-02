<?php

namespace Hproject;

final readonly class RotateCounterclockwiseCommand implements CommandInterface
{
    public function __construct(
        private Rotateable $rotateable,
        private float $angle,
    ) {}

    public function execute(): void
    {
        $this->rotateable->setVelocity($this->rotateable->getVelocity()->rotateCounterclockwise(deg2rad($this->angle)));
    }
}