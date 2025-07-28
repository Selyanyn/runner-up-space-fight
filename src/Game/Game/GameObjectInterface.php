<?php

namespace Hproject\Game\Game;

use Hproject\Game\GameObject\PresentOnFieldInterface;

/**
 * Интерфейс игрового объекта. Они могут кардинально друг от друга отличаться, но все они должны быть сериализуемы.
 */
interface GameObjectInterface extends PresentOnFieldInterface
{
    public function getId(): int;

    /**
     * @return array<string, mixed>
     */
    public function toJsonAgentResponse(): array;
}
