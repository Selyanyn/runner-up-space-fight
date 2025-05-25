<?php

namespace Hproject;

use Throwable;
use InvalidArgumentException;

final class CommandExceptionHandler
{
    /** @var array<class-string, array<class-string, CommandInterface>> */
    public array $handlerDictionary = [];

    public function handle(CommandInterface $command, Throwable $exception, int $tries): CommandInterface
    {
        if (!isset($this->handlerDictionary[$command::class][$exception::class])) {
            throw new InvalidArgumentException('Не существует обработчика для команды ' . $command::class . ' и исключения ' . $exception::class);
        }

        return $this->handlerDictionary[$command::class][$exception::class]($command, $exception, $tries);
    }

    /**
     * @param callable<CommandInterface, Throwable, int>: CommandInterface $callback
     */
    public function register(string $command, string $exception, callable $callback)
    {
        $this->handlerDictionary[$command][$exception] = $callback;
    }
}