<?php

namespace Hproject;

final readonly class CheckFuelCommand implements CommandInterface
{
    public function __construct(
        private HasFuel $fuelObject,
        private float $fuel,
    ) {}

    public function execute(): void
    {
        if (EpsilonCompare::greaterThan($this->fuel, $this->fuelObject->getFuel())) {
            throw new CommandException("Недостаточно топлива (менее $this->fuel)");
        }
    }
}