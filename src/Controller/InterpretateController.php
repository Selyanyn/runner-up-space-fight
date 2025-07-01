<?php

namespace Hproject\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class InterpretateController
{
    /**
     * @param list<non-empty-string> $args
     */
    #[Route('/interpretate', name: 'game_interpretate')]
    public function number(
        #[MapQueryParameter] int $gameId,
        #[MapQueryParameter] int $objectId,
        #[MapQueryParameter] string $command,
        #[MapQueryParameter] array $args,
    ): Response
    {

       
        return new Response(
            '<html><body>This is body: '.$gameId.', '.$objectId.', '. $command . ', '. implode(', ', $args) . '</body></html>'
        );
    }
}