<?php

namespace Hproject;

final readonly class ObjectMover
{
    public static function moveObject(Moveable $object): void
    {
        $object->setLocation($object->getLocation()->addVector($object->getVelocity()));
    }
}