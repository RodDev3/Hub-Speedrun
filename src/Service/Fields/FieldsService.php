<?php

namespace App\Service\Fields;

use App\Entity\Fields\Fields;
use App\Entity\FieldTypes\FieldTypes;
use App\Form\Fields\FieldsType;
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

    /**
     * @return FieldsType[]
     */
    public function getAllFieldsTypes(): array
    {
        return $this->entityManager->getRepository(FieldTypes::class)->findAll();
    }
}