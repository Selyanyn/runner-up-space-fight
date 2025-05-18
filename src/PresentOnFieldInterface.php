<?php

namespace Hproject;

/**
 * У любого объекта на поле существует положение в пространстве и скорость (даже если и нулевая).
 */
interface PresentOnFieldInterface
{
    public function getLocation(): FlatVector;

    public function getVelocity(): FlatVector;
}