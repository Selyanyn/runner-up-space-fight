<?php

namespace Hproject\Authorization\Controller;

use Hproject\Authorization\User\UserRepositoryInterface;
use Hproject\Infrastructure\Interpretate\InterpretateCommandStrategyRegistry;
use Hproject\Infrastructure\IoC\InitIoCContainerActionStrategyRegistry;
use Hproject\Infrastructure\Jwt\JwtHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AuthorizationController extends AbstractController
{
    /**
     * Эндпойнт авторизации пользователя.
     */
    #[Route('/login', name: 'login')]
    public function login(
        #[MapRequestPayload] LoginDto $loginDto,
        JwtHandler $jwtHandler,
        UserRepositoryInterface $userRepository,
    ): Response {
        InitIoCContainerActionStrategyRegistry::init();
        InterpretateCommandStrategyRegistry::init();

        $user = $userRepository->getByLogin($loginDto->login);
        if (null === $user || !$user->isPasswordValid($loginDto->password)) {
            return new Response('Неверные логин или пароль', 401);
        }

        $jwt = $jwtHandler->createJwtToken(['userId' => $user->id]);

        return new Response($jwt, 200);
    }
}
