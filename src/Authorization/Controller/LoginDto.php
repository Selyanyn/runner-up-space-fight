<?php

namespace Hproject\Authorization\Controller;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $login,

        #[Assert\NotBlank]
        public string $password,
    ) {
    }
}