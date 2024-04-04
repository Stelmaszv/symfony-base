<?php


namespace App\Generic\Auth;

use Symfony\Flex\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

trait AuthenticationAPi
{
    private JWT $security;

    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    #[Route(path: '/token', name: 'app_login')]
    public function getToken() :JsonResponse
    {
        $tokenStorage = $this->container->get('security.token_storage');
        $token = $tokenStorage->getToken();
        if($token === null){
            return new JsonResponse(['error'=> 'tokken requried'],JsonResponse::HTTP_UNAUTHORIZED);
        }
        $user = $token->getUser();

        $userPayload = [
            'id ' =>  $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ];

        return new JsonResponse([
            'token' => $this->jwt->encode($userPayload)
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \LogicException('This route should not be called directly.');
    }
}