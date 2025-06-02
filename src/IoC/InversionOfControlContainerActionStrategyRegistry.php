<?php

namespace Hproject\IoC;

final class InversionOfControlContainerActionStrategyRegistry
{
    private $strategies = [];

    /**
     * @param callable(IoCScope): IoCScope $strategy
     */
    public function registerStrategy(string $key, callable $strategy): void
    {
        $this->strategies[$key] = $strategy;
    }

    public function isValidStrategy(string $key): bool
    {
        return array_key_exists($key, $this->strategies);
    }

    /**
     * @return callable(IoCScope): IoCScope
     */
    public function getStrategy(string $key): callable
    {
        if (!array_key_exists($key, $this->strategies)) {
            throw new \InvalidArgumentException('Неизвестный ключ стратегии: ' . $key);
        }

        return $this->strategies[$key];
    }
}