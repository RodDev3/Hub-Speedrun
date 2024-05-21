<?php

namespace App\Service\Users;

use App\Entity\Users\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UsersService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

}