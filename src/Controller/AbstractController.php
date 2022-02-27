<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;

abstract class AbstractController extends BaseAbstractController
{
    public function getUser(): ?User
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getUser();
    }
}
