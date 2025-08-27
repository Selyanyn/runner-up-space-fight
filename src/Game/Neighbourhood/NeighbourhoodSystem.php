<?php

declare(strict_types=1);

namespace Hproject\Game\Neighbourhood;

use Hproject\Game\Game\GameObjectInterface;
use Hproject\Infrastructure\Math\FlatVector;

final class NeighbourhoodSystem
{

    /**
     * @var array<non-empty-string, Neighbourhood>
     */
    private array $neighbourhoods = [];

    public function __construct(
        public FlatVector $offset,
        public float $raduis,
    ) {
    }

    public function writeGameObject(GameObjectInterface $gameObject): void
    {
        // Очищаем от потенциальных дублей
        foreach ($this->neighbourhoods as $neighbourhood) {
            if ($neighbourhood->isObjectPresent($gameObject)) {
                $neighbourhood->removeObject($gameObject);
                break;
            }
        }

        // Ищем подходящую окрестность
        foreach ($this->neighbourhoods as $neighbourhood) {
            if ($neighbourhood->tryWriteObject($gameObject)) {
                return;
            }
        }

        // Если таковой нет, создаём ближайшую подходящую окрестность
        $newNeighbourhoodX = round(($gameObject->getLocation()->x + $this->offset->x) / $this->raduis) * $this->raduis - $this->offset->x;
        $newNeighbourhoodY = round(($gameObject->getLocation()->y + $this->offset->y) / $this->raduis) * $this->raduis - $this->offset->y;
        $newNeighbourhood = new Neighbourhood(
            center: new FlatVector($newNeighbourhoodX, $newNeighbourhoodY),
            raduis: $this->raduis,
        );

        // Защита от дурака
        if (!$newNeighbourhood->tryWriteObject($gameObject)) {
            throw new \RuntimeException('В созданную окрестность не удалось записать игровой объект');
        }
        $this->neighbourhoods[] = $newNeighbourhood;
    }

    /**
     * @return array<non-empty-string, Neighbourhood>
     */
    public function getNeighbourhoods(): array
    {
        return $this->neighbourhoods;
    }
}
