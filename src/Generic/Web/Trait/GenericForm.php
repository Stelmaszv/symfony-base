<?php

namespace App\Generic\Web\Trait;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

trait GenericForm
{
    private FormFactoryInterface $formFactory;
    private Request $request;
    protected ?string $twig = null;
    protected ?string $form = null;

    protected function onSetAttribute(): array
    {
        return [];
    }

    private function getAttributes(): array
    {
        $attributes['form'] = $this->setFormToAttribute();

        return array_merge($attributes, $this->onSetAttribute());
    }

    private function setFormToAttribute()
    {
        return $this->setForm($this->item)->createView();
    }

    private function setForm(object $entetiy) : mixed 
    {        
        $form = $this->formFactory->create($this->form, $entetiy);

        $form->handleRequest($this->request);

        return $form;
    }

    protected function onSubmittedTrue(): void {}

    protected function onSubmittedFalse(): void {}

    protected function onValid(): void {}

    protected function onInvalid(): void {}

    protected function onBeforeValid(): void {}

    protected function onAfterValid(): void {}
}
