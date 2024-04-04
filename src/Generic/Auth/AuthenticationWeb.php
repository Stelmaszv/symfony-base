<?php
namespace App\Generic\Auth;

use App\Entity\User;
use App\Roles\RoleUser;
use Symfony\Component\Uid\Uuid;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Generic\Api\Identifier\Interfaces\IdentifierUid;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

trait AuthenticationWeb
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function loginAction(AuthenticationUtils $authenticationUtils){

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \LogicException('This route should not be called directly.');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(ManagerRegistry $managerRegistry,Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $authenticationEntity = new User();
        $idetikatorUid = $authenticationEntity instanceof IdentifierUid;

        $form = $this->createForm(RegisterType::class, $authenticationEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $userPasswordHasher->hashPassword(
                $authenticationEntity,
                $form->get('password')->getData()
            );

            if($idetikatorUid){
                $authenticationEntity->setId(Uuid::v4());
            }

            $authenticationEntity->setPassword($hashedPassword);
            $authenticationEntity->setRoles([RoleUser::NAME]);

            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($authenticationEntity);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
