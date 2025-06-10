<?php

namespace Hproject\IoC;

/**
 * Класс для инициализации IoC-контейнера стандартными командами: регистрация и смена скоупа.
 *
 * В реальном приложении подобные вещи будут прописываться в точке входа, но здесь, с учётом
 * тестов и неизменяемости самих правил, можно вынести эту конфигурацию и сюда.
 */
final readonly class InitIoCContainerActionStrategyRegistry
{
    public static function init()
    {
        InversionOfControlContainer::init();

        InversionOfControlContainer::addStrategy('register', function(IoCScope $scope, ...$params) {
            $alias = $params[0];
            $registerCallback = $params[1]; 
            $scope->registerStrategy($alias, $registerCallback);
            return $scope;
        });

        InversionOfControlContainer::addStrategy('addScope', function(IoCScope $scope, ...$params) {
            $alias = $params[0];
            $childScope = new IoCScope($scope);
            $scope->addChildScope($alias, $childScope);
            return $scope;
        });

        InversionOfControlContainer::addStrategy('goToChildScope', function(IoCScope $scope, ...$params) {
            $alias = $params[0];
            return $scope->getChildScope($alias);
        });

        InversionOfControlContainer::addStrategy('goToParentScope', function(IoCScope $scope, ...$params) {
            return $scope->getParentScope();
        });
    }
}