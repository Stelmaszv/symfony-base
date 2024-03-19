<?php

declare(strict_types=1);

namespace App\Generic\Api\Controllers;

use App\Generic\Api\Trait\GenericAction;
use App\Generic\Api\Trait\GenericJSONResponse;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Generic\Api\Trait\Security as SecurityTrait;
use Symfony\Component\Security\Core\Security;

class GenericActionController extends AbstractController
{
    use GenericAction;
    use GenericJSONResponse;
    use SecurityTrait;
    protected ManagerRegistry $managerRegistry;

    private Security $security;

    public function __invoke(ManagerRegistry $managerRegistry,Security $security): JsonResponse
    {
        $this->managerRegistry = $managerRegistry;
        $this->security = $security;

        return $this->view('executeAction');
    }

    private function executeAction(): JsonResponse
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