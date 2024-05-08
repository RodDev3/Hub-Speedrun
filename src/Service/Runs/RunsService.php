<?php

namespace App\Service\Runs;

use App\Entity\FieldData\FieldData;
use App\Entity\Fields\Fields;
use App\Entity\Games\Games;
use App\Entity\Runs\Runs;
use App\Entity\Status\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class RunsService extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function runsValidation(Runs $runs, Request $request): JsonResponse
    {

        foreach ($request->request->all()['run'] as $key => $otherField) {
            $field = $this->entityManager->getRepository(Fields::class)->find($key);

            //TODO FAIRE ALL CHAMPS
            switch ($field->getRefFieldTypes()->getBackName()) {
                case 'time-goal':
                    $totalMilli = 0;

                    foreach ($otherField as $typeTimes => $times) {

                        //Validation sur les entrées utilisateurs
                        if (!is_numeric($times) && $times !== "") {
                            return new JsonResponse(['message' => 'Invalid time entered'], 400);
                        }

                        $times = (int)$times;

                        if ($times < 0) {
                            return new JsonResponse(['message' => 'Invalid time entered'], 400);
                        }

                        //Conversion en ms
                        switch ($typeTimes){
                            case 'hours':
                                $times = $times*1000*60*60;
                                break;
                            case 'minutes':
                                $times = $times*1000*60;
                                break;
                            case 'secondes':
                                $times = $times*1000;
                                break;
                        }

                        $totalMilli += $times;
                    }

                    $fieldData = new FieldData();
                    $fieldData->setRefFields($field)
                        ->setRefRuns($runs)
                        ->setData($totalMilli)
                    ;

                    $this->entityManager->persist($fieldData);

                    break;
            }
        }

        $runs->setUuid(Uuid::v4());
        $runs->setRefStatus($this->entityManager->getRepository(Status::class)->find(1));

        $this->entityManager->persist($runs);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Run submitted !'], 200);
    }
}