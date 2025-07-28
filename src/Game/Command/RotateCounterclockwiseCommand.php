<?php

namespace Hproject\Game\Command;

use Hproject\Infrastructure\Command\CommandInterface;
use Hproject\Game\GameObject\Rotateable;

final readonly class RotateCounterclockwiseCommand implements CommandInterface
{
    public function __construct(
        private Rotateable $rotateable,
        private float $angle,
    ) {
    }

    public function execute(): void
    {
        $this->rotateable->setVelocity($this->rotateable->getVelocity()->rotateCounterclockwise(deg2rad($this->angle)));
    }
}
