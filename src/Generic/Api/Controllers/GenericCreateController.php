<?php

namespace App\Generic\Api\Controllers;

use App\Generic\Api\Trait\GenericJSONResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Generic\Api\Interfaces\ApiInterface;
use App\Generic\Api\Trait\GenericValidation;
use Symfony\Component\HttpFoundation\Request;
use App\Generic\Api\Interfaces\GenricInterface;
use App\Generic\Api\Trait\GenericProcessEntity;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Generic\Api\Trait\Security as SecurityTrait;

class GenericCreateController extends AbstractController implements GenricInterface
{
    use GenericValidation;
    use GenericProcessEntity;
    use GenericJSONResponse;
    use SecurityTrait;

    public function __invoke(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $managerRegistry): JsonResponse
    {
        $this->initialize($request, $serializer, $validator, $managerRegistry);
        $this->checkData();

        return $this->view('createAction');
    }

    protected function initialize(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $managerRegistry): void
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
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