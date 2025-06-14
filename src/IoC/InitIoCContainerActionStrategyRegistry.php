<?php

namespace Hproject\IoC;

use Hproject\Moveable;
use ReflectionClass;
use ReflectionNamedType;

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
            return new IoCActionResult($scope);
        });

        InversionOfControlContainer::addStrategy('addScope', function(IoCScope $scope, ...$params) {
            $alias = $params[0];
            $childScope = new IoCScope($scope);
            $scope->addChildScope($alias, $childScope);
            return new IoCActionResult($scope);
        });

        InversionOfControlContainer::addStrategy('goToChildScope', function(IoCScope $scope, ...$params) {
            $alias = $params[0];
            $childScope = $scope->getChildScope($alias);
            return new IoCActionResult($childScope, $childScope);
        });

        InversionOfControlContainer::addStrategy('goToParentScope', function(IoCScope $scope, ...$params) {
            $parentScope = $scope->getParentScope();
            return new IoCActionResult($parentScope, $parentScope);
        });

        InversionOfControlContainer::addStrategy('adapter', function(IoCScope $scope, ...$params) {
            $interfaceClass = $params[0];
            $object = $params[1];

            // Название анонимного класса адаптера. Внутреннее название класса PHP не парсится.
            $className = str_replace('\\', '_', $interfaceClass . time());
            if (class_exists($className)) {
                return $className;
            }

            $interfaceReflectionClass = new ReflectionClass($interfaceClass);
            $methods = $interfaceReflectionClass->getMethods();
            $methodsForEval = '';

            /*
            * В целом, возможности рефлексии PHP весьма ограничены - там можно проивзводить чтение свойств
            * и простейшие операции рода смены видимости, но создать класс или переопределить методы не выйдет.
            * Остаётся два пути:
            * - использовать eval с костылями;
            * - использовать mock из тестовых библиотек. Я не пошёл по этому пути, так как это тестовые библиотеки,
            * для разработки не предназначенные, и в них пришлось бы сверху разбираться.
            */
            foreach ($methods as $method) {
                // Формирование сигнатуры метода
                $methodString = (string) $method;
                preg_match('~^Method \[ <.*>(?P<name>.*?) \]~', $methodString, $methodName);
                $methodName = trim($methodName['name']);
                if (str_starts_with($methodName, 'abstract ')) {
                    $methodName = substr($methodName, 9);
                }
                $methodName = str_replace(' method ', ' function ', $methodName);

                // Формирование параметров метода
                preg_match_all('~Parameter #\d+ \[ <.*>(?P<params>.*?) \]~', $methodString, $params);
                $params = implode(', ', $params['params']);

                // Формирование возвращаемого типа метода
                preg_match('~Return \[ (?P<type>.*?) \]~', $methodString, $returnType);
                $body = $methodName . '(' . $params . '): ' . $returnType['type'];

                // Формирование тела метода
                $methodParams = $method->getParameters();
                $paramsForEvalArray = [];
                foreach ($methodParams as $methodParam) {
                    $paramsForEvalArray[] = '$' . $methodParam->getName();
                }
                
                $resolveBindingName = $interfaceClass . '::' . $method->getName();
                $returnType = $method->getReturnType();
                $isVoid = $returnType instanceof ReflectionNamedType && $returnType->getName() === 'void';
                $methodsForEval .= "\n" . $body . " { " . ($isVoid ? '' : 'return ') . InversionOfControlContainer::class . "::resolve('$resolveBindingName', \$this->object" . (
                    count($paramsForEvalArray) > 0
                        ? ', ' . implode(', ', $paramsForEvalArray)
                        : ''
                ) . "); }";
            }

            $class = "
                class $className implements $interfaceClass {
                    public function __construct(
                        private \$object,
                    ) {}

                    $methodsForEval
                }
            ";

            eval($class);

            return new IoCActionResult($scope, new $className($object));
        });
    }
}