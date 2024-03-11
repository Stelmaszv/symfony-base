<?php

declare(strict_types=1);

namespace App\Generic\Api\Controllers;

use App\Generic\Api\Trait\GenericAction;
use App\Generic\Api\Trait\GenericJSONResponse;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenericActionController extends AbstractController
{
    use GenericAction;
    use GenericJSONResponse;
    private string $successMessage = 'Action Executed successfully';
    protected ManagerRegistry $managerRegistry;

    public function __invoke(ManagerRegistry $managerRegistry): JsonResponse
    {
        $this->managerRegistry = $managerRegistry;

        return $this->update();
    }

    private function update(): JsonResponse
    {
        $this->beforeAction();
        $this->action();
        $this->afterAction();

        return $this->respondWithSuccess(JsonResponse::HTTP_OK);
    }

    protected function getRepository(string $entity): ObjectRepository
    {
        return $this->managerRegistry->getRepository($entity);
    }
}