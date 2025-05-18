<?php

namespace Hproject;

final readonly class ObjectRotator
{
    public static function rotateObjectCounterclockwise(Rotateable $object, float $angle): void
    {
        $object->setVelocity($object->getVelocity()->rotateCounterclockwise($angle));
    }
}