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
    protected int $perPage = 0;
    protected ManagerRegistry $managerRegistry;
    private SerializerInterface $serializer;
    private int $id = 0;

    public function __invoke(ManagerRegistry $doctrine, SerializerInterface $serializer, int $id): JsonResponse
    {
        $this->initialize($doctrine, $serializer, $id);
        return $this->getAction();
    }

    protected function initialize(ManagerRegistry $doctrine, SerializerInterface $serializer, int $id): void
    {
        $this->managerRegistry = $doctrine;
        $this->serializer = $serializer;
        $this->id = $id;
    }

    protected function onQuerySet(ObjectRepository $repository): ?object
    {
        return $repository->find($this->id);
    }

    private function getAction(): JsonResponse
    {
        $car = $this->getObject();

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
