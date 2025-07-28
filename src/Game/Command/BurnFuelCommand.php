<?php

namespace Hproject\Game\Command;

use Hproject\Game\GameObject\HasFuel;
use Hproject\Infrastructure\Command\CommandInterface;

final readonly class BurnFuelCommand implements CommandInterface
{
    public function __construct(
        private HasFuel $fuelObject,
        private float $fuel,
    ) {
    }

    public function execute(): void
    {
        $this->fuelObject->burnFuel($this->fuel);
    }
}
