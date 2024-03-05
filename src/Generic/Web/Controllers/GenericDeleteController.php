<?php

namespace App\Generic\Web\Controllers;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class GenericDeleteController extends AbstractController
{
    protected ?string $entity = null;
    protected ?string $redirectTo = null;
    protected EntityManagerInterface $entityManager;
    protected ServiceEntityRepository $repository;
    protected object $item;
    protected int $id;

    public function __invoke(EntityManagerInterface $entityManager, int $id): Response
    {
        $this->initialize($entityManager, $id);
        return $this->deleteAction();
    }

    protected function initialize(EntityManagerInterface $entityManager, int $id): void
    {
        $this->entityManager = $entityManager;
        $this->id = $id;
        $this->repository = $this->entityManager->getRepository($this->entity);
        $this->checkData();
    }

    protected function beforeDelete(): void {}
    protected function afterDelete(): void {}
    protected function setRedirect(): void {}
    protected function setFlashMessage(): void {}

    private function deleteAction(): Response
    {
        $this->delete();

        return new Response('Object was destroy');
    }

    private function delete(): void
    {
        $this->beforeDelete();
        $this->entityManager->remove($this->item);
        $this->entityManager->flush();
        $this->afterDelete();
    }

    private function checkData(): void
    {
        $item = $this->entityManager->getRepository($this->entity)->find($this->id);

        if (!$item) {
            throw $this->createNotFoundException('Not found with id: ' . $this->id);
        }

        $this->item = $item;

        if (!$this->entity) {
            throw new \Exception("Entity is not defined in controller " . get_class($this) . "!");
        }
    }
}
