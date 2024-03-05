<?php

namespace App\Generic\Web\Controllers;

use App\Generic\Web\Trait\GenericForm;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenericCreateController extends AbstractController
{
    use GenericForm;
    
    private ManagerRegistry $doctrine; 
    protected ?string $entity = null;
    protected object $item;

    public function __invoke(FormFactoryInterface $formFactory, ManagerRegistry $doctrine, Request $request): Response
    {
        $this->initialize($formFactory, $doctrine, $request);
        $this->checkData();

        return $this->createAction();
    }

    protected function initialize(FormFactoryInterface $formFactory, ManagerRegistry $doctrine, Request $request){
        $this->doctrine = $doctrine;
        $this->request = $request;
        $this->formFactory = $formFactory;
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

    private function createAction(): Response
    {
        $entity = new $this->entity();
        $form = $this->setForm($entity);
        $this->item = $entity; 
        
        if ($this->request->isMethod('POST')) {
            if ($form->isSubmitted()) {
                $this->onSubmittedTrue();
                $this->onBeforeValid();
                if ($form->isValid()) {
                    $this->onValid();
                    $this->save();
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

    private function save() : void {
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($this->item);
        $entityManager->flush();
    } 
}
