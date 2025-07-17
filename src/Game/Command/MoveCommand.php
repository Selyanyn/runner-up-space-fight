<?php

namespace Hproject\Game\Command;

use Hproject\Infrastructure\Command\CommandInterface;
use Hproject\Game\GameObject\Moveable;

final readonly class MoveCommand implements CommandInterface
{
    public function __construct(
        private Moveable $moveable,
    ) {
    }

    public function execute(): void
    {
        $this->moveable->setLocation($this->moveable->getLocation()->addVector($this->moveable->getVelocity()));
    }
}
