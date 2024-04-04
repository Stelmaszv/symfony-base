<?php

declare(strict_types=1);

namespace App\Generic\Api\Controllers;

use App\Generic\Auth\JWT;
use App\Generic\Api\Interfaces\DTO;
use App\Generic\Api\Trait\GenericAction;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use App\Generic\Api\Trait\GenericValidation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Generic\Api\Trait\GenericJSONResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Generic\Api\Trait\Security as SecurityTrait;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class GenericPostController extends AbstractController
{
    use GenericValidation;
    use GenericJSONResponse;
    use GenericAction;
    use SecurityTrait;
    
    protected ?string $dto = null;
    protected ManagerRegistry $managerRegistry;
    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;
    protected Request $request;
    private Security $security;

    public function __invoke(
            Request $request,
            SerializerInterface $serializer, 
            ValidatorInterface $validator,
            ManagerRegistry $managerRegistry, 
            Security $security,
            TokenStorageInterface $token,
            JWT $jwt
        ): JsonResponse
    {
        $this->initialize($request, $serializer, $validator, $managerRegistry,$security);

        return $this->setSecurityView('postAction',$jwt);
    }

    protected function initialize(
            Request $request, 
            SerializerInterface $serializer, 
            ValidatorInterface $validator, 
            ManagerRegistry $managerRegistry, 
            Security $security
        ): void
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->security = $security;
        $this->managerRegistry = $managerRegistry;
        $this->request = $request;
    }

    private function postAction(): JsonResponse
    {
        $this->beforeAction();

        $data = $this->request->getContent();

        if (empty($data)) {
            return $this->respondWithError('No data provided', JsonResponse::HTTP_BAD_REQUEST);
        }

        $dto = $this->deserializeDto($data);

        $this->beforeValidation();
        $errors = $this->validateDto($dto);
        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }
        
        $this->action();
        $this->afterValidation();
        $this->afterAction();

        return $this->respondWithSuccess(JsonResponse::HTTP_OK);
    }

    protected function getRepository(string $entity): ObjectRepository
    {
        return $this->managerRegistry->getRepository($entity);
    }
}
