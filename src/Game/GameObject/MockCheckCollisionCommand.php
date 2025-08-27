<?php

declare(strict_types=1);

namespace Hproject\Game\GameObject;

use Hproject\Infrastructure\Command\CommandInterface;

class MockCheckCollisionCommand implements CommandInterface
{
    private static int $mockCollisionCounter = 0;

    public function __construct(
        private PresentOnFieldInterface $gameObjectA,
        private PresentOnFieldInterface $gameObjectB,
    ) {
    }

    public function execute(): void
    {
        // Здесь будет более точные сравнения и логика столкновения объектов. Пока что лежит такая заглушка.
        if ($this->gameObjectA->getLocation()->isEqualWithEpsilon($this->gameObjectB->getLocation())) {
            self::$mockCollisionCounter++;
        }
    }

    public static function getMockCollisionCounter(): int
    {
        return self::$mockCollisionCounter;
    }
}
