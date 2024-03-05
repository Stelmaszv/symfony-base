<?php

namespace App\Generic\Web\Controllers;

use App\Generic\Web\Trait\GenericForm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class GenericUpdateController extends AbstractController
{
    use GenericForm;
    private ManagerRegistry $doctrine;
    private EntityManagerInterface $entityManager; 
    private int $id;
    protected ?string $entity = null;
    protected object $item;
    protected ServiceEntityRepository $repository;

    public function __invoke(FormFactoryInterface $formFactory,EntityManagerInterface $entityManager,ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $this->initialize($formFactory,$entityManager, $doctrine, $request, $id);
        $this->checkData();

        return $this->editAction();
    }

    private function checkData(): void
    {
        if (!$this->form) {
            throw new \Exception("Form is not defined in controller " . get_class($this) . "!");
        }

        if (!$this->entity) {
            throw new \Exception("Entity is not defined in controller " . get_class($this) . "!");
        }

        if (!$this->twig) {
            throw new \Exception("Twig is not defined in controller " . get_class($this) . "!");
        }
    }

    protected function initialize(FormFactoryInterface $formFactory,EntityManagerInterface $entityManager,ManagerRegistry $doctrine, Request $request, int $id){
        $this->formFactory = $formFactory;
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->id = $id;
        $this->repository = $this->entityManager->getRepository($this->entity);
        $item = $this->repository->find($this->id);

        if (!$item) {
            throw $this->createNotFoundException('Not Found');
        }

        $this->item  = $item;
    }

    private function editAction(): Response
    {
        $form = $this->setForm($this->item);
        
        if ($this->request->isMethod('POST')) {
            if ($form->isSubmitted()) {
                $this->onSubmittedTrue();
                $this->onBeforeValid();
                if ($form->isValid()) {
                    $this->onValid();
                    $this->doctrine->getManager()->flush();
                    $this->onAfterValid();
                } else {
                    $this->onInvalid();
                }
            } else {
                $this->onSubmittedFalse();
            }
        }

        return $this->render($this->twig, $this->getAttributes());
    }
}
