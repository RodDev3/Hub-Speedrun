<?php

namespace App\Service\Categories;

use App\Entity\Categories\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoriesService
{


    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}


    public function getCategoriesFromRewrite(string $rewrite): array
    {
        return $this->entityManager->getRepository(Categories::class)->getCategoriesFromRewrite($rewrite);
    }
}
