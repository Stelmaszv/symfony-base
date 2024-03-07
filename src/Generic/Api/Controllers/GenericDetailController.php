<?php

namespace App\Generic\Api\Controllers;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenericDetailController extends AbstractController
{
    protected ?string $entity = null;
    protected ManagerRegistry $managerRegistry;
    protected ObjectRepository $repository;
    private SerializerInterface $serializer;
    private int $id = 0;

    public function __invoke(ManagerRegistry $managerRegistry, SerializerInterface $serializer, int $id): JsonResponse
    {
        if(!$this->entity) {
            throw new \Exception("Entity is not define in controller ".get_class($this)."!");
        }

        $this->initialize($managerRegistry, $serializer, $id);

        return $this->getAction();
    }

    protected function initialize(ManagerRegistry $managerRegistry, SerializerInterface $serializer, int $id): void
    {
        $this->managerRegistry = $managerRegistry;
        $this->serializer = $serializer;
        $this->id = $id;
        $this->repository = $this->managerRegistry->getRepository($this->entity);
    }

    protected function onQuerySet(): ?object
    {
        return $this->repository->find($this->id);
    }

    protected function beforeQuery() :void {}

    protected function afterQuery() :void {}

    private function getAction(): JsonResponse
    {
        $this->beforeQuery();
        $car = $this->getObject();
        $this->afterQuery();

        if (!$car) {
            return new JsonResponse(['message' => 'Car not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse($this->normalize($car), JsonResponse::HTTP_OK);
    }

    private function normalize(object $object): array
    {
        return $this->serializer->normalize($object, null, [
            'groups' => 'api',
            'circular_reference_handler' => function () {
                return null;
            },
        ]);
    }

    private function getObject(): ?object
    {
        return $this->onQuerySet($this->managerRegistry->getRepository($this->entity));
    }
}
