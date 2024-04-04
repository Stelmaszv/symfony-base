<?php


namespace App\Generic\Auth;

use App\Entity\User;
use App\Roles\RoleUser;
use Symfony\Flex\Response;
use Symfony\Component\Uid\Uuid;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Generic\Api\Identifier\Interfaces\IdentifierUid;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

trait AuthenticationAPi
{
    private JWT $security;

    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    #[Route('/api/logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \LogicException('This route should not be called directly.');
    }

    #[Route('/api/login', name: 'login', methods: ['POST'])]
    public function login(Request $request,ManagerRegistry $doctrine): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['message' => 'Invalid data Login'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = $doctrine?->getRepository(User::class)?->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return new JsonResponse(['message' => 'Invalid data Login'], JsonResponse::HTTP_UNAUTHORIZED);
        }
     
        if (!password_verify($data['password'], $user->getPassword())) {
            return new JsonResponse(['message' => 'Invalid data Login or password'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'token' => $this->generateToken($user)
        ]);
    }

    #[Route('/api/register', name: 'register', methods: ['POST'])]
    public function register(Request $request,ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $authenticationEntity = new User();
        $idetikatorUid = $authenticationEntity instanceof IdentifierUid;

        $issetData = isset($data['email']) && isset($data['password']) && isset($data['repeatpassword']);
        $emptyData = empty($data['email']) && empty($data['password']) && empty($data['repeatpassword']);
        
        if(!$issetData && !$emptyData){
            return new JsonResponse(['message' => 'No data provided'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if($user){
            return new JsonResponse(['message' => 'User Exist!'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $hashedPassword = $userPasswordHasher->hashPassword(
            $authenticationEntity,
            $data['password']
        );

        if($idetikatorUid){
            $authenticationEntity->setId(Uuid::v4());
        }

        $authenticationEntity->setEmail($data['email']);
        $authenticationEntity->setPassword($hashedPassword);
        $authenticationEntity->setRoles([RoleUser::NAME]);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($authenticationEntity);
        $entityManager->flush();


        return new JsonResponse([
            'token' => $this->generateToken($authenticationEntity)
        ]);
    }

    private function isPasswordValid(UserInterface $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }

    private function generateToken(UserInterface $user): string
    {
        $userPayload = [
            'id ' =>  $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ];

        return $this->jwt->encode($userPayload);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function loginAction(AuthenticationUtils $authenticationUtils){
        throw new \LogicException('Login Action');
    }
}