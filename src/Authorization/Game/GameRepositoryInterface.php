<?php

declare(strict_types=1);

namespace Hproject\Authorization\Game;

/**
 * Определение игры в микросервисе авторизации и игровом микросервисе может отличаться друг от друга.
 */
interface GameRepositoryInterface
{
    /**
     * @param non-empty-list<positive-int> $playersIds
     */
    public function createGame(array $playersIds): Game;

    public function getById(int $id): ?Game;
}
