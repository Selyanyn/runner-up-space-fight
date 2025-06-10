<?php

namespace Hproject\IoC;

final class InversionOfControlContainer
{
    private static IoCScope $scope;

    private static InversionOfControlContainerActionStrategyRegistry $actionStrategyRegistry;

    public static function init(): void
    {
        self::$scope = new IoCScope();
        self::$actionStrategyRegistry = new InversionOfControlContainerActionStrategyRegistry();
    }

    public static function resolve(
        string $key,
        mixed ...$params,
    ) {
        if (self::$actionStrategyRegistry->isValidStrategy($key)) {
            self::$scope = self::$actionStrategyRegistry->getStrategy($key)(self::$scope, ...$params);
            return;
        }

        return self::$scope->getStrategy($key)(...$params);
    }

    public static function addStrategy(string $key, callable $strategy): void
    {
        self::$actionStrategyRegistry->registerStrategy($key, $strategy);
    }
}