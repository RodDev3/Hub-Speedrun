<?php

namespace App\Service\BootService;

use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment;

class BootService
{
    public function __construct(
        private readonly Environment $twig,
        private readonly Security $security
    ){
        $this->initUser();
    }

    private function initUser(): void
    {
        $this->twig->addGlobal('USER', $this->security->getUser());
    }
}