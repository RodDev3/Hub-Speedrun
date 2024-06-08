<?php

namespace App\Service\Security;


use App\Entity\Roles\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class SecurityService
{
    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $entityManager,
    )
    {
        $this->setRolesConst();
    }

    //Set les roles en Const à chaque page
    private function setRolesConst(): void
    {
        $roles = $this->entityManager->getRepository(Roles::class)->findAll();
        foreach ($roles as $role) {
            define('ROLE_'.$role->getKeyName() . '_RANK', $role->getRankOrder());
            $this->twig->addGlobal('ROLE_'.$role->getKeyName() . '_RANK', $role->getRankOrder());
        }
    }
}