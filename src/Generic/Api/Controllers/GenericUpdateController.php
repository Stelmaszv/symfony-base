<?php

namespace App\Generic\Api\Controllers;

use App\Generic\Api\Trait\GenericTrait;
use Doctrine\Persistence\ManagerRegistry;
use App\Generic\Api\Interfaces\ApiInterface;
use App\Generic\Api\Trait\GenericValidation;
use Symfony\Component\HttpFoundation\Request;
use App\Generic\Api\Interfaces\GenricInterface;
use App\Generic\Api\Trait\GenericJSONResponse;
use App\Generic\Api\Trait\GenericProcessEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Generic\Api\Trait\Security as SecurityTrait;

class GenericUpdateController extends AbstractController implements GenricInterface
{
    use GenericValidation;
    use GenericProcessEntity;
    use GenericJSONResponse;
    use SecurityTrait;

    protected int $id;

    public function __invoke(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $managerRegistry, int $id): JsonResponse
    {
        $this->initialize($request, $serializer, $validator, $managerRegistry, $id);
        $this->checkData();

        return $this->view('updateAction');
    }

    protected function initialize(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $managerRegistry, int $id): void
    {
        $this->request = $request;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->managerRegistry = $managerRegistry;
        $this->id = $id;
    }

    private function updateAction(): JsonResponse
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

        return $this->respondWithSuccess(JsonResponse::HTTP_OK);
    }

    public function getEntity() : ApiInterface {
        return $this->managerRegistry->getRepository($this->entity)->find($this->id);
    }
}
