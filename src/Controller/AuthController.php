<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route(path: '/api/login', name: 'app_login', methods: ['POST'])]
    public function login(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }

    #[Route(path: '/api/verify_token', name: 'api_verify_token', methods: ['GET'])]
    public function verifyToken(TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        if ($token && $token->getUser() instanceof UserInterface) {
            // Le token est valide
            return new JsonResponse(['valid' => true], Response::HTTP_OK);
        }
        // Le token n'est pas valide
        return new JsonResponse(['valid' => false], Response::HTTP_UNAUTHORIZED);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
