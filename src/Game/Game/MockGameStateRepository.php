<?php

namespace Hproject\Game\Game;

use Hproject\Game\GameObject\Spaceship;
use Hproject\Infrastructure\Math\FlatVector;
use Hproject\Infrastructure\Queue\CommandQueue;

/**
 * Пример реализации репозитория. Настоящее приложение ходило бы в хранилище данных, например, в базу данных
 * (здесь были бы вызовы ORM или прямые SQL-методы).
 */
final readonly class MockGameStateRepository implements GameStateRepositoryInterface
{
    private Game $game;

    public function __construct()
    {
        $gameObject = new Spaceship(
            new FlatVector(5.0, 5.0),
            new FlatVector(3.0, 1.0),
            10.0,
        );
        $this->game = new Game(
            1,
            new CommandQueue(),
            new GameState([
                1 => $gameObject,
            ]),
            [1, 2],
        );
    }

    public function getGame(int $id): ?Game
    {
        return $id === 1 ? $this->game : null;
    }

    public function getGameObject(int $gameId, int $objectId): ?GameObjectInterface
    {
        return $this->game->gameState->gameObjects[$objectId] ?? null;
    }
}
