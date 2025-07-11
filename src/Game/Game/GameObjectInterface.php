<?php

namespace Hproject\Game\Game;

/**
 * Интерфейс игрового объекта. Они могут кардинально друг от друга отличаться, но все они должны быть сериализуемы.
 */
interface GameObjectInterface
{
    public function toJsonAgentResponse(): array;
}
