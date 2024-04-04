<?php

namespace App\Generic\Api\Controllers;

use ReflectionClass;
use App\Generic\Auth\JWT;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use App\Generic\Api\Interfaces\ApiInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Generic\Api\Trait\Security as SecurityTrait;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenericDetailController extends AbstractController
{
    use SecurityTrait;
    
    protected ?string $entity = null;
    protected ManagerRegistry $managerRegistry;
    protected ObjectRepository $repository;
    private SerializerInterface $serializer;
    private null|int|string $id = 0;
    private Security $security;
    protected array $columns = [];

    public function __invoke(
            ManagerRegistry $managerRegistry, 
            SerializerInterface $serializer,
            Security $security, 
            null|int|string $id,
            JWT $jwt
        ): JsonResponse
    {
        if(!$this->entity) {
            throw new \Exception("Entity is not define in controller ".get_class($this)."!");
        }

        $this->initialize($managerRegistry, $serializer,$security, $id);

        return $this->setSecurityView('detailAction',$jwt);
    }

    protected function initialize(
            ManagerRegistry $managerRegistry, 
            SerializerInterface $serializer,
            Security $security, 
            null|int|string $id
        ): void
    {
        $this->managerRegistry = $managerRegistry;
        $this->serializer = $serializer;
        $this->security = $security;
        $this->id = $id;
        $this->repository = $this->managerRegistry->getRepository($this->entity);
    }

    protected function onQuerySet(): ?object
    {
        return $this->repository->find($this->id);
    }

    protected function beforeQuery() :void {}

    protected function afterQuery() :void {}

    private function detailAction(): JsonResponse
    {
        $this->beforeQuery();
        $car = $this->getObject();
        $this->afterQuery();

        if (!$car) {
            return new JsonResponse(['message' => 'Object not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse($this->normalize($car), JsonResponse::HTTP_OK);
    }

    private function normalize(ApiInterface $object): array
    {
        return $this->serializer->normalize($this->setData($object), null, []);
    }

    private function getObject(): ?object
    {
        return $this->onQuerySet();
    }

    private function setData(ApiInterface $entity) : array
    {

        $reflection = new ReflectionClass($entity);

        $result = [];

            foreach($reflection->getProperties() as $property){
                if(count($this->columns) == 0 || (in_array($property->getName() ,$this->columns) && in_array($property->getName() ,$this->columns)) ){
                    $method = 'get' . ucfirst($property->getName());
                    $result[$property->getName()] = $entity->$method();
                }
            }

        return $result;
    }
}
