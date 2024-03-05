<?php

namespace App\Generic\Web\Controllers;

use Doctrine\ORM\EntityManagerInterface;
use App\Generic\Web\Trait\GenericGetTrait;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class GenericListController extends AbstractController
{
    use GenericGetTrait;

    private ?array $paginatorData = null;
    private bool $paginate = false;
    protected ?int $perPage = null;
    protected Request $request;
    protected EntityManagerInterface $entityManager;
    protected PaginatorInterface $paginator;
    protected ServiceEntityRepository $repository;

    public function __invoke(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $this->initialize($request, $entityManager, $paginator);
        return $this->listAction();
    }

    protected function initialize(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): void
    {
        $this->checkData();
        $this->request = $request;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->repository = $this->entityManager->getRepository($this->entity);
        $this->paginate = ($this->perPage !== null && $this->perPage !== 0);
    }

    protected function onQuerySet(): mixed
    {
        return $this->repository->findAll();
    }

    private function getQuery(): mixed
    {
        $this->beforeQuery();
        $queryBuilder = $this->onQuerySet();
        $pagination = $this->paginator->paginate($queryBuilder, $this->request->query->getInt('page', 1), $this->perPage);
        $this->afterQuery();

        $this->paginatorData = $pagination->getPaginationData();

        return ($this->perPage !== null && $this->perPage !== 0) ? $pagination : $queryBuilder;
    }

    private function getAttributes(): array
    {
        $attributes['object'] = $this->getQuery();
        $attributes['paginate'] = $this->paginate;
        $attributes['paginatorData'] = $this->paginatorData;

        return array_merge($attributes, $this->onSetAttribute());
    }

    private function listAction(): Response
    {
        return $this->render($this->twig, $this->getAttributes());
    }
}