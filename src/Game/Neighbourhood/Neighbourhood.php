<?php

declare(strict_types=1);

namespace Hproject\Game\Neighbourhood;

use Hproject\Game\Game\GameObjectInterface;
use Hproject\Infrastructure\Math\FlatVector;

/**
 * Квадратная окрестность. Например, если её центр находится в точке (3,4), а радиус окрестности - 2, она занимает
 * квадрат с угловыми координатами (1, 2) - (5, 2) - (5, 6) - (1, 6)
 */
final class Neighbourhood
{
    /**
     * @var array<positive-int, GameObjectInterface>
     */
    private array $gameObjects = [];

    public function __construct(
        public FlatVector $center,
        public float $raduis,
    ) {
    }

    public function isLocationInNeighbourhood(FlatVector $location): bool
    {
        return abs($this->center->x - $location->x) < $this->raduis
            && abs($this->center->y - $location->y) < $this->raduis;
    }

    public function tryWriteObject(GameObjectInterface $object): bool
    {
        if (!$this->isLocationInNeighbourhood($object->getLocation())) {
            return false;
        }

        $this->gameObjects[$object->getId()] = $object;
        return true;
    }

    public function isObjectPresent(GameObjectInterface $object): bool
    {
        return array_key_exists($object->getId(), $this->gameObjects);
    }

    public function removeObject(GameObjectInterface $object): void
    {
        unset($this->gameObjects[$object->getId()]);
    }

    /**
     * @return array<positive-int, GameObjectInterface>
     */
    public function gameObjects(): array
    {
        return $this->gameObjects;
    }
}
