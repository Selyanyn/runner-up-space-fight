<?php

namespace Hproject\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Hproject\Interpretate\InterpretateCommand;
use Hproject\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\Interpretate\InterpretateCommandStrategyRegistry;
use Hproject\Game\GameStateRepositoryInterface;
use Symfony\Component\Routing\Attribute\Route;

class InterpretateController
{
    public function __construct(
        private GameStateRepositoryInterface $gameStateRepository,
    ) {}

    /**
     * Эндпойнт сделан в виде контроллера.
     * В том числе реализован ответ агенту в виде json-состояния игры.
     *
     * @param list<non-empty-string> $args
     */
    #[Route('/interpretate', name: 'game_interpretate')]
    public function number(
        #[MapQueryParameter] int $gameId,
        #[MapQueryParameter] int $objectId,
        #[MapQueryParameter] string $command,
        #[MapQueryParameter] array $args,
    ): JsonResponse {
        InitIoCContainerActionStrategyRegistry::init();
        InterpretateCommandStrategyRegistry::init();

        $game = $this->gameStateRepository->getGame($gameId);
        $gameObject = $this->gameStateRepository->getGameObject($gameId, $objectId);
        
        $interpretateCommand = new InterpretateCommand(
            $game,
            $gameObject,
            $command,
            $args,
        );
        $interpretateCommand->execute();
       
        $gameStateObjects = [];
        foreach ($game->gameState->gameObjects as $key => $object) {
            $gameStateObjects[$key] = $object->toJsonAgentResponse();
        }
        return new JsonResponse(json_encode($gameStateObjects));
    }
}