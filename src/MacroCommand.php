<?php

namespace Hproject;

final readonly class MacroCommand implements CommandInterface
{
    /**
     * @list<CommandInterface>
     */
    public function __construct(
        private array $commands,
    ) {}

    public function execute(): void
    {
        foreach ($this->commands as $command) {
            $command->execute();
        }
    }
}