<?php

namespace Hproject\Interpretate;

use Hproject\CommandInterface;
use Hproject\Game\Game;
use Hproject\Game\GameObjectInterface;
use Hproject\IoC\InversionOfControlContainer;
use Hproject\Queue\CommandQueueInterface;

/**
 * Команда интерпретации.
 */
final readonly class InterpretateCommand implements CommandInterface
{
    public function __construct(
        private Game $game,
        private GameObjectInterface $gameObject,
        private string $commandName,
        private array $args,
    ) {}

    public function execute(): void
    {
        $iocCommandName = 'interpretate.' . $this->commandName;
        $command = InversionOfControlContainer::resolve(
            $iocCommandName,
            $this->gameObject,
            ...$this->args,
        );

        $this->game->commandQueue->enqueue($command);
    }
}