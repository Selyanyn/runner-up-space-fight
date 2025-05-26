<?php

namespace Hproject;

final readonly class ChangeVelocityCommand implements CommandInterface
{
    public function __construct(
        private VelocityChangeable $velocityObject,
        private float $xModifier,
        private float $yModifier,
    ) {}

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