<?php

namespace Hproject\Game;

use Hproject\FlatVector;
use Hproject\Queue\CommandQueue;
use Hproject\Spaceship;

/**
 * Пример реализации репозитория. Настоящее приложение ходило бы в хранилище данных, например, в базу данных
 * (здесь были бы вызовы ORM или прямые SQL-методы).
 */
final readonly class GameStateRepository implements GameStateRepositoryInterface
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
            new CommandQueue(),
            new GameState([
                1 => $gameObject,
            ])
        );
    }

    public function getGame(int $id): Game
    {
        return $this->game;
    }

    public function getGameObject(int $gameId, int $objectId): GameObjectInterface
    {
        return $this->game->gameState->gameObjects[$objectId] ?? throw new \Exception();
    }
}