<?php

namespace App\Service\Fields;

use App\Entity\Fields\Fields;
use App\Entity\FieldTypes\FieldTypes;
use App\Form\Fields\FieldsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Uid\Uuid;

class FieldsService extends AbstractController
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

    public function getNewFields(FieldTypes $fieldTypes):JsonResponse
    {
        $field = '';
        switch ($fieldTypes->getBackName()){
            case 'time-goal':
                $field = $this->renderView('categories/add/timeGoal.html.twig');
                break;
            case 'number-goal':
                $field = $this->renderView('categories/add/numberGoal.html.twig');
            case 'select':
                $field = $this->renderView('categories/add/select.html.twig',[
                    'uuid' => Uuid::v4()
                ]);
        }
        return new JsonResponse($field);
    }
}