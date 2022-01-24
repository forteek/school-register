<?php

namespace App\Entity;

use App\Entity\Component\Contract\Identifiable;
use App\Entity\Component\Contract\Timestampable;
use App\Entity\Component\Trait\Identifiable as IdentifiableTrait;
use App\Entity\Component\Trait\Timestampable as TimestampableTrait;

abstract class AbstractEntity implements Identifiable, Timestampable
{
    use IdentifiableTrait;
    use TimestampableTrait;
}