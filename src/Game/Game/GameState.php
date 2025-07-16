<?php

namespace Hproject\Game\Game;

final class GameState
{
    /**
     * @param array<int, GameObjectInterface> $gameObjects
     */
    public function __construct(
        public array $gameObjects,
    ) {
    }
}
