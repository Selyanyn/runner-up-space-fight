<?php

namespace Hproject\Interpretate;

use Hproject\IoC\InversionOfControlContainer;
use Hproject\MacroCommand;
use Hproject\CheckFuelCommand;
use Hproject\MoveCommand;
use Hproject\BurnFuelCommand;

final readonly class InterpretateCommandStrategyRegistry
{
    // Все допустимые команды будут регистрироваться с префиксом interpretate. . Это позволит
    // предотвратить потенциальные инъекции со стороны пользователей и не заводить отдельный парсер.
    public static function init(): void
    {
        InversionOfControlContainer::resolve(
            'register',
            "interpretate.move",
            fn ($spaceship, float $fuel) => new MacroCommand([
                new CheckFuelCommand($spaceship, $fuel),
                new BurnFuelCommand($spaceship, $fuel),
                new MoveCommand($spaceship),
            ]),
        );
    }
}