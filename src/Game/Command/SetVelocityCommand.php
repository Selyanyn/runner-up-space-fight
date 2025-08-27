<?php

namespace Hproject\Game\Command;

use Hproject\Game\GameObject\VelocityChangeable;
use Hproject\Infrastructure\Command\CommandInterface;
use Hproject\Infrastructure\Math\FlatVector;

final readonly class SetVelocityCommand implements CommandInterface
{
    public function __construct(
        private VelocityChangeable $velocityObject,
        private FlatVector $velocity,
    ) {
    }

    public function execute(): void
    {
        $this->velocityObject->setVelocity($this->velocity);
    }
}
