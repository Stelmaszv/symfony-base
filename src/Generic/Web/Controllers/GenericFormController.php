<?php

namespace App\Generic\Web\Controllers;

use App\Generic\Web\Trait\GenericForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GenericFormController extends AbstractController
{
    use GenericForm; 

    public function __invoke(FormFactoryInterface $formFactory, Request $request): Response
    {
        $this->initialize($formFactory, $request);
        $this->checkData();

        return $this->formAction();
    }

    protected function initialize(FormFactoryInterface $formFactory, Request $request): void
    {
        $this->formFactory = $formFactory;
        $this->request = $request;
    }
    
    protected function formAction(): Response
    {
        $form = $this->formFactory->create($this->form);
        $form->handleRequest($this->request);

        if ($form->isSubmitted()) {
            $this->onSubmittedTrue();
            $this->onBeforeValid();
            if ($form->isValid()) {
                $this->onValid();
                $this->onAfterValid();
            } else {
                $this->onInvalid();
            }
        } else {
            $this->onSubmittedFalse();
        }

        return $this->render($this->twig, $this->getAttributes());
    }

    private function checkData(): void
    {
        if (!$this->form) {
            throw new \Exception("Form is not defined in controller " . get_class($this) . "!");
        }

        if (!$this->twig) {
            throw new \Exception("Twig is not defined in controller " . get_class($this) . "!");
        }
    }
}
