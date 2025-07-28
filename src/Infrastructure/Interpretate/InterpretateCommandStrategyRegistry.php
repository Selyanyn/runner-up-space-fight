<?php

namespace Hproject\Infrastructure\Interpretate;

use Hproject\Game\Command\BurnFuelCommand;
use Hproject\Game\Command\CheckFuelCommand;
use Hproject\Game\Command\MoveCommand;
use Hproject\Infrastructure\Command\MacroCommand;
use Hproject\Infrastructure\IoC\InversionOfControlContainer;

final readonly class InterpretateCommandStrategyRegistry
{
    // Все допустимые команды будут регистрироваться с префиксом interpretate. . Это позволит
    // предотвратить потенциальные инъекции со стороны пользователей и не заводить отдельный парсер.
    public static function init(): void
    {
        InversionOfControlContainer::resolve(
            'register',
            'interpretate.move',
            fn ($spaceship, float $fuel) => new MacroCommand([
                new CheckFuelCommand($spaceship, $fuel),
                new BurnFuelCommand($spaceship, $fuel),
                new MoveCommand($spaceship),
            ]),
        );
    }
}
