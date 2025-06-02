<?php

namespace Hproject\IoC;

use InvalidArgumentException;

final class IoCScope
{
    /**
     * @var array<non-empty-string, callable>
     */
    private array $strategies;

    private ?IoCScope $parentScope;

    /**
     * @var array<non-empty-string, IoCScope>
     */
    private array $chlidScopes;

    public function __construct(?IoCScope $parentScope = null)
    {
        $this->strategies = [];
        $this->chlidScopes = [];
        $this->parentScope = $parentScope;
    }

    public function registerStrategy(string $key, callable $callback)
    {
        $this->strategies[$key] = $callback;
    }

    public function getStrategy(string $key): callable
    {
        if (array_key_exists($key, $this->strategies)) {
            return $this->strategies[$key];
        }

        if ($this->parentScope === null) {
            throw new IoCScopeException('Неизвестный ключ стратегии: ' . $key);
        }

        return $this->parentScope->getStrategy($key);
    }

    public function addChildScope(string $key, IoCScope $scope): void
    {
        $this->chlidScopes[$key] = $scope;
    }

    public function getChildScope(string $key): IoCScope
    {
        return $this->chlidScopes[$key];
    }

    public function getParentScope(): IoCScope
    {
        return $this->parentScope;
    }
}