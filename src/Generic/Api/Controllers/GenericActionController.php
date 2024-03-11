<?php

declare(strict_types=1);

namespace App\Generic\Api\Controllers;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class GenericActionController extends AbstractController 
{
    private string $successMessage = 'Action Executed successfully';

    protected ManagerRegistry $managerRegistry;

    public function __invoke(ManagerRegistry $managerRegistry): JsonResponse
    {
        $this->managerRegistry = $managerRegistry;

        return $this->update();
    }

    private function update() : JsonResponse{
        $this->beaforeAction();
        $this->action();
        $this->afterAction();

        return $this->respondWithSuccess(JsonResponse::HTTP_OK);
    }

    private function respondWithSuccess(int $statusCode): JsonResponse
    {
        $responseData = ['success' => true, 'message' => $this->successMessage];
        $responseData = array_merge($responseData,$this->onSuccessResponseMessage());

        return new JsonResponse($responseData, $statusCode);
    }

    protected function onSuccessResponseMessage() : array {
        return [];
    }

    protected function getRepository(string $entity) : ObjectRepository{
        return $this->managerRegistry->getRepository($entity);
    }

    protected function beaforeAction() : void {}

    abstract protected function action() : void;

    protected function afterAction() : void {}
}
