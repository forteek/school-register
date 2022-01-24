<?php

namespace App\Entity\Component\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait Identifiable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private readonly int $id;

    public function getId(): int
    {
        return $this->id;
    }
}
