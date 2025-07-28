<?php

declare(strict_types=1);

namespace Hproject\Game\Neighbourhood;

use Hproject\Game\Game\GameObjectInterface;
use Hproject\Game\GameObject\MockCheckCollisionCommand;
use Hproject\Infrastructure\Command\CommandInterface;
use Hproject\Infrastructure\Command\MacroCommand;
use Hproject\Infrastructure\Queue\CommandQueueInterface;

final readonly class CheckGameObjectNeighbourhoodCommand implements CommandInterface
{
    public function __construct(
        private NeighbourhoodSystem $system,
        private GameObjectInterface $gameObject,
        private CommandQueueInterface $commandQueue,
    ) {
    }

    public function execute(): void
    {
        // Запись / перезапись объекта в систему окрестностей
        $this->system->writeGameObject($this->gameObject);
        $neighbourhoods = $this->system->getNeighbourhoods();

        // Поиск окрестности с объектом
        $neighbourhoodWithObject = null;
        foreach ($neighbourhoods as $neighbourhood) {
            if ($neighbourhood->isObjectPresent($this->gameObject)) {
                $neighbourhoodWithObject = $neighbourhood;
            }
        }
        if ($neighbourhoodWithObject === null) {
            throw new \RuntimeException('Ни одна из окрестностей не содержит объект');
        }

        // Формирование команд на проверку коллизий
        $checkCollisionCommands = [];
        foreach ($neighbourhoodWithObject->gameObjects() as $gameObject) {
            if ($this->gameObject->getId() === $gameObject->getId()) {
                continue;
            }

            $checkCollisionCommands[] = new MockCheckCollisionCommand(
                $this->gameObject,
                $gameObject,
            );
        }

        // Установка макрокоманды в очередь
        $this->commandQueue->enqueue(new MacroCommand($checkCollisionCommands));
    }
}
