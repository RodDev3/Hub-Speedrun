<?php

namespace App\Service\Fields;

use App\Entity\Fields\Fields;
use App\Repository\Fields\FieldsRepository;
use Doctrine\ORM\EntityManagerInterface;

class FieldsService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ){}

    public function getFieldsFromCategories(int $id): array
    {
        return $this->entityManager->getRepository(Fields::class)->getFieldsFromCategories($id);
    }
}