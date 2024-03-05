<?php

namespace App\Generic\Web\Trait;

trait GenericGetTrait
{
    protected ?string $entity = null;
    protected ?string $twig = null;

    protected function beforeQuery() : void {}

    protected function afterQuery() : void{}

    protected function onSetAttribute() : array
    {
      return [];
    }

    private function checkData() : void
    {
        if(!$this->entity) {
            throw new \Exception("Entity is not define in controller ".get_class($this)."!");
        }

        if(!$this->twig) {
            throw new \Exception("Twing is not define in controller ".get_class($this)."!");
        }
    }
}
