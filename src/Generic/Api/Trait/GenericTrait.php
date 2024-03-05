<?php

namespace App\Generic\Api\Trait;

use ReflectionClass;
use App\Generic\Api\Interfaces\DTO;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait GenericTrait
{
    protected ?string $entity = null;
    protected ?string $dto = null;

    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;
    protected ManagerRegistry $doctrine;
    protected Request $request;

    private function deserializeDto(string $data)
    {
        return $this->serializer->deserialize($data, $this->dto, 'json');
    }

    private function validateDto(DTO $dto): array
    {
        return iterator_to_array($this->validator->validate($dto));
    }

    private function processEntity(DTO $dto): void
    {
        $entity = $this->getEntity();
        $reflectionClass = new ReflectionClass($entity);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyType = $property->getType();

            if ($propertyName !== 'id') {
                $propertyTypeName = $propertyType->__toString();
                $object = $this->getObject($propertyTypeName);
                $method = 'set' . ucfirst($propertyName);

                if ($object !== null && property_exists($dto, 'company') && $dto->company !== null) {
                    $objectRepository = $this->doctrine->getRepository($object::class);
                    $entity->setCompany($objectRepository->find($dto->company));
                } else {
                    $entity->$method($dto->$propertyName);
                }
            }
        }

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    private function getObject(string $type): ?object
    {
        $type = ltrim($type, '?');

        if (strpos($type, '\\') === false) {
            return null;
        }

        $nameSpace = '\\' . $type;

        return new $nameSpace;
    }

    private function respondWithError(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse(['errors' => ['message' => $message]], $statusCode);
    }

    private function validationErrorResponse(array $errors): JsonResponse
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
    }

    private function respondWithSuccess(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse(['message' => $message], $statusCode);
    }
}
