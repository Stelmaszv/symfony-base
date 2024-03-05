<?php

namespace App\Generic\Api\Controllers;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenericDeleteController extends AbstractController
{
    protected ?string $entity = null;

    protected ManagerRegistry $managerRegistry;
    protected int $id = 0;

    public function __invoke(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $this->initialize($doctrine,$id);
        return $this->deleteAction($doctrine,$id);
    }

    public function deleteAction(ManagerRegistry $doctrine, int $id): JsonResponse
    {

        $car = $doctrine->getRepository($this->entity)->find($id);
    
        if (!$car) {
            return new JsonResponse(['message' => 'Car not found'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        $this->beforeDelete();
        $this->delete($car);
        $this->afterDelete();
    
        return new JsonResponse(['message' => 'Car deleted successfully'], JsonResponse::HTTP_OK);
    }

    protected function initialize(ManagerRegistry $doctrine, int $id): void
    {
        $this->managerRegistry = $doctrine;
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