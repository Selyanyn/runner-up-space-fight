<?php

namespace Hproject\Game;

final class GameState
{
    /**
     * @param array<int, GameObjectInterface> $gameObjects
     */
    public function __construct(
        public array $gameObjects,
    ) {}
}