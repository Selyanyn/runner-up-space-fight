<?php

declare(strict_types=1);

namespace Hproject\Authorization\Game;

final readonly class Game
{
    /**
     * @param non-empty-list<positive-int> $playersIds
     */
    public function __construct(
        public int $id,
        public array $playersIds,
    ) {
    }
}
