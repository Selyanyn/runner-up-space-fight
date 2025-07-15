<?php

declare(strict_types=1);

namespace Hproject\Authorization\Controller;

use Hproject\Authorization\Game\GameRepositoryInterface;
use Hproject\Infrastructure\Jwt\JwtHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    /**
     * Эндпойнт создания игры.
     *
     * @param non-empty-list<positive-int> $userIds
     */
    #[Route('/game/create', name: 'create_game')]
    public function createGame(
        #[MapQueryParameter] array $userIds,
        Request $request,
        JwtHandler $jwtHandler,
        GameRepositoryInterface $gameRepository,
    ): JsonResponse {
        $jwtHandler->validateAndCheckClaims(
            jwtTokenRaw: $request->headers->get('Authorization'),
            claims: null,
        );

        $game = $gameRepository->createGame($userIds);

        return new JsonResponse(['id' => $game->id], 200);
    }

    /**
     * Эндпойнт получения JWT-токена для игрового сервиса.
     */
    #[Route('/game/access_token', name: 'get_game_access_token')]
    public function getGameAccessToken(
        #[MapQueryParameter] int $gameId,
        Request $request,
        JwtHandler $jwtHandler,
        GameRepositoryInterface $gameRepository,
    ): Response {
        $userId = $jwtHandler->validateAndCheckClaims(
            jwtTokenRaw: $request->headers->get('Authorization'),
            claims: function (\stdClass $jwtData) use ($gameRepository, $gameId) {
                $userId = $jwtData->userId;
                $game = $gameRepository->getById($gameId);
                if (!in_array($userId, $game->playersIds)) {
                    throw new UnprocessableEntityHttpException('Пользователь не относится к участникам игры');
                }
            },
        );

        $jwt = $jwtHandler->createJwtToken([
            'userId' => $userId,
            'gameId' => $gameId,
        ]);

        return new Response($jwt, 200);
    }
}
