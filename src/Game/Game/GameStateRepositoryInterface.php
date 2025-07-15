<?php

namespace Hproject\Game\Game;

/**
 * Интерфейс для получения информации об игре из хранилища.
 */
interface GameStateRepositoryInterface
{
    public function getGame(int $id): ?Game;

    public function getGameObject(int $gameId, int $objectId): ?GameObjectInterface;
}
