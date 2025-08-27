<?php

namespace Hproject\Infrastructure\IoC;

final readonly class IoCActionResult
{
    public function __construct(
        public IoCScope $currentScope,
        public mixed $result = null,
    ) {
    }
}
