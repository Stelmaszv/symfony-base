<?php


namespace App\Generic\Api\Identifier\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;

trait IdentifierByUid
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }
}