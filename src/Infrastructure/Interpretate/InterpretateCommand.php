<?php

namespace Hproject\Infrastructure\Interpretate;

use Hproject\Game\Game\Game;
use Hproject\Game\Game\GameObjectInterface;
use Hproject\Infrastructure\Command\CommandInterface;
use Hproject\Infrastructure\IoC\InversionOfControlContainer;

/**
 * Команда интерпретации.
 */
final readonly class InterpretateCommand implements CommandInterface
{
    /**
     * @param Game $game
     * @param GameObjectInterface $gameObject
     * @param string $commandName
     * @param list<mixed> $args
     */
    public function __construct(
        private Game $game,
        private GameObjectInterface $gameObject,
        private string $commandName,
        private array $args,
    ) {
    }

    public function execute(): void
    {
        $iocCommandName = 'interpretate.'.$this->commandName;
        $command = InversionOfControlContainer::resolve(
            $iocCommandName,
            $this->gameObject,
            ...$this->args,
        );

        $this->game->commandQueue->enqueue($command);
    }
}
