<?php

namespace Hproject;

final readonly class MoveCommand implements CommandInterface
{
    public function __construct(
        private Moveable $moveable,
    ) {}

    public function execute(): void
    {
        $this->moveable->setLocation($this->moveable->getLocation()->addVector($this->moveable->getVelocity()));
    }
}