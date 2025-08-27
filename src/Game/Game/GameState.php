<?php

namespace Hproject\Game\Game;

final class GameState
{
    /** @var array<positive-int, GameObjectInterface> */
    private array $gameObjects;

    /** @var array<positive-int, list<positive-int>> */
    private array $gameObjectsOwnership;

    /**
     * @param non-empty-list<positive-int> $owners
     */
    public function addGameObject(GameObjectInterface $gameObject, array $owners): void
    {
        $this->gameObjects[$gameObject->getId()] = $gameObject;
        $this->gameObjectsOwnership[$gameObject->getId()] = $owners;
    }

    /**
     * @param non-empty-list<positive-int> $playersIds
     */
    public function getGameObject(int $id, array $playersIds): ?GameObjectInterface
    {
        if ([] === array_intersect($playersIds, $this->gameObjectsOwnership[$id] ?? [])) {
            return null;
        }

        return $this->gameObjects[$id];
    }

    /**
     * @param non-empty-list<positive-int> $playersIds
     * @return list<GameObjectInterface>
     */
    public function getAllGameObjects(array $playersIds): array
    {
        $result = [];
        foreach ($this->gameObjects as $gameObject) {
            if ([] !== array_intersect($playersIds, $this->gameObjectsOwnership[$gameObject->getId()])) {
                $result[] = $gameObject;
            }
        }

        return $result;
    }

    public function generateNextId(): int
    {
        return (end($this->gameObjects)->getId() ?: 0) + 1;
    }
}
