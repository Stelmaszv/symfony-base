<?php

namespace App\Generic\Api\Controllers;

use App\Generic\Auth\JWT;
use Doctrine\Persistence\ManagerRegistry;
use App\Generic\Api\Interfaces\ApiInterface;
use App\Generic\Api\Trait\GenericValidation;
use App\Generic\Api\Interfaces\ProcessEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Generic\Api\Trait\GenericJSONResponse;
use App\Generic\Api\Interfaces\GenricInterface;
use App\Generic\Api\Trait\GenericProcessEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Generic\Api\Trait\Security as SecurityTrait;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenericCreateController extends AbstractController implements GenricInterface, ProcessEntity
{
    use GenericValidation;
    use GenericProcessEntity;
    use GenericJSONResponse;
    use SecurityTrait;

    private Security $security;

    public function __invoke(
            Request $request, 
            SerializerInterface $serializer, 
            ValidatorInterface $validator, 
            ManagerRegistry $managerRegistry,
            Security $security,
            JWT $jwt,
        ): JsonResponse
    {
        $this->initialize($request, $serializer, $validator, $managerRegistry,$security);
        $this->checkData();

        return $this->setSecurityView('createAction',$jwt);
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

    private function createAction(): JsonResponse
    {
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
        $this->afterValidation();

        $this->processEntity($dto);
        $this->afterProcessEntity();

        return $this->respondWithSuccess(JsonResponse::HTTP_CREATED);
    }

    public function getEntity() : ApiInterface {
        return new $this->entity();
    }
}