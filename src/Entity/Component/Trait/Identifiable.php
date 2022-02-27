<?php

namespace App\Entity\Component\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait Identifiable
{

    public function getId(): int
    {
        return $this->id;
    }
}
