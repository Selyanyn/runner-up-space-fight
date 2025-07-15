<?php

namespace Hproject\Game\Controller;

use Hproject\Game\Game\GameStateRepositoryInterface;
use Hproject\Infrastructure\Interpretate\InterpretateCommand;
use Hproject\Infrastructure\Interpretate\InterpretateCommandStrategyRegistry;
use Hproject\Infrastructure\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\Infrastructure\Jwt\JwtHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;

class InterpretateController extends AbstractController
{
    public function __construct(
        private GameStateRepositoryInterface $gameStateRepository,
    ) {
    }

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
        Request $request,
        JwtHandler $jwtHandler,
    ): JsonResponse {
        $jwtHandler->validateAndCheckClaims(
            jwtTokenRaw: $request->headers->get('Authorization'),
            claims: function (\stdClass $jwtToken) use ($gameId) {
                $userId = $jwtToken->userId;
                $jwtGameId = $jwtToken->gameId;
                $game = $this->gameStateRepository->getGame($gameId);
                if ($jwtGameId !== $gameId || !$game || !in_array($userId, $game->playersIds)) {
                    throw new UnauthorizedHttpException('Невозможно выполнить операцию');
                }
            },
        );

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
