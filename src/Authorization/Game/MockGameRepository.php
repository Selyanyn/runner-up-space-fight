<?php

declare(strict_types=1);

namespace Hproject\Authorization\Game;

class MockGameRepository implements GameRepositoryInterface
{
    public function createGame(array $playersIds): Game
    {
        return new Game(
            id: 1,
            playersIds: $playersIds,
        );
    }

    public function getById(int $id): ?Game
    {
        return match ($id) {
            1 => new Game(
                id: 1,
                playersIds: [1, 2],
            ),
            default => null,
        };
    }
}
