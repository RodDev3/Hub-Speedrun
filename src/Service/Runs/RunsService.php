<?php

namespace App\Service\Runs;

use App\Entity\Categories\Categories;
use App\Entity\FieldData\FieldData;
use App\Entity\Fields\Fields;
use App\Entity\Games\Games;
use App\Entity\Runs\Runs;
use App\Entity\Status\Status;
use App\Entity\Users\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\Clock\now;

class RunsService extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function runsValidation(Runs $runs, Request $request): JsonResponse
    {

        foreach ($request->request->all()['fields'] as $key => $fieldData) {
            //Cas spécifique avec les users
            if (explode('.', $key)[0] === 'users'){

                //Empty
                if ($fieldData === ""){
                    return new JsonResponse(['message' => 'Please enter all runners'],400);
                }

                $user = $this->entityManager->getRepository(Users::class)->findOneBy(['username' => $fieldData]);
                //Invalid User
                if (!$user instanceof Users) {
                    return new JsonResponse(['message' => 'Invalid user entered'],400);
                }

                //Already exists
                if ($runs->getRefUsers()->contains($user)) {
                    return new JsonResponse(['message' => 'Runner already exists'],400);
                }
                $runs->addRefUser($user);
                continue;
            }

            $field = $this->entityManager->getRepository(Fields::class)->findOneBy(['uuid' => $key]);
            if (!$field instanceof Fields) {
                return new JsonResponse(['message' => 'Invalid field'],400);
            }

            switch ($field->getRefFieldTypes()->getBackName()) {
                case 'time-goal':
                    $totalMilli = 0;

                    foreach ($fieldData as $typeTimes => $times) {

                        $times === "" && $times = 0;

                        //Validation sur les entrées utilisateurs
                        if (!is_numeric($times)) {
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


                    if ($totalMilli <= 0){
                        return new JsonResponse(['message' => 'Please entered a valid ' . $field->getConfig()['label'] . ' time'], 400);
                    }

                    $fieldData = new FieldData($field, $runs, $totalMilli);

                    $this->entityManager->persist($fieldData);

                    break;

                case 'select':
                    //Check if value send is same as database
                    if (!in_array($fieldData, $field->getConfig()['options'])){
                        return new JsonResponse(['message' => 'Invalid value in '. $field->getConfig()['label'] . ' list'], 400);
                    }

                    $fieldData = new FieldData($field, $runs, $fieldData);

                    $this->entityManager->persist($fieldData);
                    
                    break;
            }
        }

        //if user set date in the future
        if ($runs->getDateMade() > now()){
            return new JsonResponse(['message' => 'Invalid date'],400);
        }

        //check video mandatory
        if ($runs->getRefCategories()->isVideoMandatory()){
            if ($runs->getVideo() === null){
                return new JsonResponse(['message' => 'Please fill the video url'], 400);
            }
        }

        //Set pending
        $runs->setRefStatus($this->entityManager->getRepository(Status::class)->find(1));
        $runs->setDateSubmitted(now());

        $this->entityManager->persist($runs);


        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Run submitted !' ,'redirect' => $this->generateUrl('app_games_show', ['rewrite' => $runs->getRefGame()->getRewrite()])], 200);
    }

    public function checkOldAndNewRun(Runs $run)
    {
        $subCategoriesValue = $run->getSubCategoriesData($run->getRefCategories());

        //Les users de la run
        $users = $run->getRefUsers();

        //All runs active de la catégorie
        $commonRunsCategory = $this->entityManager->getRepository(Runs::class)->findByUsersAndCategory($users, $run->getRefCategories());

        $commonRunsSubCategories = [];

        //Trie pour n'avoir que les runs qui match les sous catégorie
        foreach ($commonRunsCategory as $runCategory){
            if ($runCategory->getSubCategoriesData($runCategory->getRefCategories()) === $subCategoriesValue){
                $commonRunsSubCategories[] = $runCategory;
            }
        }

        if (count($commonRunsSubCategories) > 0){
            //Il y a un pb existant
            $prevRun = $commonRunsCategory[0];

            $primaryField = $run->getRefCategories()->getPrimaryComparison();
            $secondaryField = $run->getRefCategories()->getSecondaryComparison();


            if ($run->getDataFromField($primaryField)->getData() === $prevRun->getDataFromField($primaryField)->getData()){
                //same primary as pb
                if ($secondaryField === null){
                    //All same as pb but new pb
                    $prevRun->setRefStatus($this->entityManager->getRepository(Status::class)->find(4));
                    $run->setRefStatus($this->entityManager->getRepository(Status::class)->find(2));

                }else{

                    if ($run->getDataFromField($secondaryField)->getData() <= $prevRun->getDataFromField($secondaryField)->getData()){
                        //Beat pb or same as pb so new pb
                        $prevRun->setRefStatus($this->entityManager->getRepository(Status::class)->find(4));
                        $run->setRefStatus($this->entityManager->getRepository(Status::class)->find(2));
                    }else {
                        //No beat pb so obsolete
                        $run->setRefStatus($this->entityManager->getRepository(Status::class)->find(4));
                    }

                }
            }else if ($run->getDataFromField($primaryField)->getData() > $prevRun->getDataFromField($primaryField)->getData()){
                //not beat pb so obsolete
                $run->setRefStatus($this->entityManager->getRepository(Status::class)->find(4));
            }else{
                //beat pb new pb
                $prevRun->setRefStatus($this->entityManager->getRepository(Status::class)->find(4));
                $run->setRefStatus($this->entityManager->getRepository(Status::class)->find(2));
            }

            $this->entityManager->persist($prevRun);
        }else{
            $run->setRefStatus($this->entityManager->getRepository(Status::class)->find(2));
        }
    }
}