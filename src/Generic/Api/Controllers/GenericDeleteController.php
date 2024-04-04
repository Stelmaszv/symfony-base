<?php

namespace App\Generic\Api\Controllers;

use App\Generic\Auth\JWT;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use App\Generic\Api\Trait\GenericJSONResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Generic\Api\Trait\Security as SecurityTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenericDeleteController extends AbstractController
{
    use SecurityTrait;
    use GenericJSONResponse;

    protected ?string $entity = null;

    private Security $security;

    protected ManagerRegistry $managerRegistry;
    protected null|int|string $id = 0;

    public function __invoke(
            ManagerRegistry $doctrine,
            Security $security, 
            null|int|string $id,
            JWT $jwt
        ): JsonResponse
    {
        $this->initialize($doctrine,$security,$id);

        return $this->setSecurityView('deleteAction',$jwt);
    }

    public function deleteAction(): JsonResponse
    {

        $car = $this->managerRegistry->getRepository($this->entity)->find($this->id);
    
        if (!$car) {
            return $this->respondWithError('Object not found',JsonResponse::HTTP_NOT_FOUND);
        }
    
        $this->beforeDelete();
        $this->delete($car);
        $this->afterDelete();

        return $this->respondWithSuccess(JsonResponse::HTTP_OK);
    }

    protected function initialize(
            ManagerRegistry $doctrine,
            Security $security, 
            null|int|string $id
        ): void
    {
        $this->managerRegistry = $doctrine;
        $this->security = $security;
        $this->id = $id;
    }

    protected function beforeDelete(): void {}

    protected function afterDelete(): void {}

    private function delete(object $car) : void 
    {
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->remove($car);
        $entityManager->flush();
    }
}