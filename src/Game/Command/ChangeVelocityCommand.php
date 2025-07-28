<?php

namespace Hproject\Game\Command;

use Hproject\Game\GameObject\VelocityChangeable;
use Hproject\Infrastructure\Command\CommandInterface;
use Hproject\Infrastructure\Math\FlatVector;

final readonly class ChangeVelocityCommand implements CommandInterface
{
    public function __construct(
        private VelocityChangeable $velocityObject,
        private float $xModifier,
        private float $yModifier,
    ) {
    }

    public function execute(): void
    {
        $this->velocityObject->setVelocity(
            new FlatVector(
                $this->velocityObject->getVelocity()->x * $this->xModifier,
                $this->velocityObject->getVelocity()->y * $this->yModifier,
            ),
        );
    }
}
