<?php

declare(strict_types=1);

namespace App\Generic\Api\Controllers;

use App\Generic\Api\Interfaces\DTO;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class GenericPostController extends AbstractController
{
    protected ?string $dto = null;
    protected ManagerRegistry $managerRegistry;
    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;

    protected Request $request;

    public function __invoke(Request $request,SerializerInterface $serializer, ValidatorInterface $validator,ManagerRegistry $managerRegistry): JsonResponse
    {
        $this->initialize($request, $serializer, $validator, $managerRegistry);

        return $this->post();
    }

    protected function initialize(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $managerRegistry): void
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->managerRegistry = $managerRegistry;
        $this->request = $request;
    }

    protected function beforeValidation(): void {}

    protected function afterValidation(): void {}

    private function validationErrorResponse(array $errors): JsonResponse
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
    }

    private function respondWithError(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse(['errors' => ['message' => $message]], $statusCode);
    }

    private function post(): JsonResponse
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

    protected function respondWithSuccess(int $statusCode): JsonResponse
    {
        $responseData = ['success' => true];
        $responseData = array_merge($responseData, $this->onSuccessResponseMessage());

        return new JsonResponse($responseData, $statusCode);
    }

    protected function onSuccessResponseMessage(): array
    {
        return [];
    }

    protected function getRepository(string $entity): ObjectRepository
    {
        return $this->managerRegistry->getRepository($entity);
    }

    private function deserializeDto(string $data)
    {
        return $this->serializer->deserialize($data, $this->dto, 'json');
    }

    private function validateDto(DTO $dto): array
    {
        return iterator_to_array($this->validator->validate($dto));
    }

    protected function beforeAction(): void {}

    abstract protected function action(): void;

    protected function afterAction(): void {}
}
