<?php

namespace App\Generic\Api\Trait;

use App\Generic\Api\Interfaces\DTO;
use Symfony\Component\HttpFoundation\JsonResponse;

trait GenericValidation
{
    protected function beforeValidation(): void {}

    protected function afterValidation(): void {}

    private function deserializeDto(string $data)
    {
        return $this->serializer->deserialize($data, $this->dto, 'json');
    }

    private function validateDto(DTO $dto): array
    {
        return iterator_to_array($this->validator->validate($dto));
    }

    private function validationErrorResponse(array $errors): JsonResponse
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
    }
}