<?php

namespace Hproject;

final class InversionOfControlContainer
{
    /**
     * @var array<non-empty-string, callable>
     */
    private array $strategies = [];

    public function resolve(
        string $key,
        ?callable $registerCallback = null,
        mixed ...$params,
    ) {
        if (isset($registerCallback)) {
            $this->strategies[$key] = $registerCallback;
            return;
        }

        return $this->strategies[$key](...$params);
    }
}