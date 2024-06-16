<?php

namespace App\Service\Categories;

use App\Entity\Categories\Categories;
use App\Entity\FieldData\FieldData;
use App\Entity\Fields\Fields;
use App\Entity\FieldTypes\FieldTypes;
use App\Entity\Runs\Runs;
use App\Entity\Status\Status;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\New_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use function Composer\Autoload\includeFile;
use function PHPUnit\Framework\isEmpty;

class CategoriesService extends AbstractController
{


    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}


    public function getCategoriesFromRewrite(string $rewrite): array
    {
        return $this->entityManager->getRepository(Categories::class)->getCategoriesFromRewrite($rewrite);
    }

    public function categoriesValidation(Request $request, Categories $categories)
    {


        $formData = $request->request->all();
        //dd($formData);

        //Vérification des données de la categories
        if ($categories->getName() === null) {
            return new JsonResponse(['message' => 'Please fill the name of the category'], 400);
        }

        if ($categories->getRules() === null) {
            return new JsonResponse(['message' => 'Please fill the rule of the category'], 400);
        }

        if ($categories->getPlayers() === null) {
            return new JsonResponse(['message' => 'Please fill the number of players'], 400);
        }

        //Normalement cast en int ou null mais à garder au cas où
        if (!is_int($categories->getPlayers())) {
            return new JsonResponse(['message' => 'Incorrect number of players'], 400);
        }

        $create = false;
        if ($categories->getUuid() === null){
            $create = true;
            $categories->setUuid(Uuid::v4());
        }


        //Persist/flush la caté
        $this->entityManager->persist($categories);


        //Vérification s'il y a bien des champs ajoutés
        if (isset ($formData['categories']['fields'])) {

            $hasPrimary = false;
            $hasSecondary = false;



            //Vérification des données sur chaque nouveau champ
            foreach ($formData['categories']['fields'] as $key => $fieldData) {


                $explodedType = explode('.', $key);
                $fieldType = $explodedType[0];

                //dd($formData['categories']['fields']);
                if ($create){
                    //Cas création de catégorie
                    $field = new Fields();
                }else{

                    if (!UuidV4::isValid($explodedType[1])){
                        //Cas Update : Ajout de field
                        $field = new Fields();
                    }else{
                        $field = $this->entityManager->getRepository(Fields::class)->findOneBy(['uuid' => $explodedType[1]]);
                    }
                    if (!$field instanceof Fields){
                        return new JsonResponse(['message' => 'An error occurred while adding field'], 400);
                    }
                    //Permet la suppression d'options désactivée ici
                    /*else{
                        $field->setConfig([]);
                    }*/
                }


                $field->setRefCategories($categories);

                switch ($fieldType) {

                    case 'time-goal':

                        $isPrimary = false;
                        $isSecondary = false;
                        //Boucle sur chaque propriété de nouveau champs
                        foreach ($fieldData as $keys => $data) {


                            switch ($keys) {
                                case 'primary':
                                    //Vérification si plusieurs champs ont le primary
                                    $isPrimary = true;
                                    if ($hasPrimary) {
                                        return new JsonResponse(['message' => 'Please select only one primary timing method'], 400);
                                    } else {
                                        $hasPrimary = true;
                                        $field->addToConfig(['primary' => true]);
                                    }
                                    break;

                                //Vérification si plusieurs champs ont le secondary
                                case 'secondary':
                                    $isSecondary = true;
                                    if ($hasSecondary) {
                                        return new JsonResponse(['message' => 'Please select only one secondary timing method'], 400);
                                    } else {
                                        $hasSecondary = true;
                                        $field->addToConfig(['secondary' => true]);
                                    }
                                    break;

                                case 'label':
                                    if ($data === "") {
                                        return new JsonResponse(['message' => 'Please fill the name of the timing method'], 400);
                                    }
                                    $field->addToConfig(['label' => $data]);
                                    break;
                            }
                        }


                        //Un champ ne peut être que primaire ou secondaire
                        if ($isPrimary && $isSecondary) {
                            return new JsonResponse(['message' => 'Please choose between primary and secondary'], 400);
                        }
                        if ($isPrimary === false && $isSecondary === false ){
                            return new JsonResponse(['message' => 'Please choose between primary and secondary'], 400);
                        }

                        //Vérif sur le type de champ
                        $type = $this->entityManager->getRepository(FieldTypes::class)->findOneBy(['backName' => $fieldType]);
                        if (!$type instanceof FieldTypes) {
                            return new JsonResponse(['message' => 'Invalid field type'], 400);
                        }
                        $field->setRefFieldTypes($type);

                        $field->setRankOrder(1);

                        $this->entityManager->persist($field);
                        break;

                    case 'select' :
                        $options = [];

                        $pointer = 0;

                        foreach ($fieldData as $key => $data) {

                            $keyExploded = explode('.', $key);
                            $key = $keyExploded[0];

                            switch ($key) {
                                case 'option':
                                    //Check si l'options est vide
                                    if ($data === '') {
                                        return new JsonResponse(['message' => "Please fill all options in the list"], 400);
                                    }

                                    if (!$create){
                                        //Cas update modification valeur de l'option
                                        if (!isset($field->getConfig()["options"]) ){
                                            dd($field);
                                        }
                                        if ($field->getConfig()["options"][$pointer] !== $data){

                                            //Récupération de toutes les ayant la data modifiée
                                            $runs = $this->entityManager->getRepository(Runs::class)->findByCategorieAndFieldAndData($categories, $field, $field->getConfig()["options"][$pointer]);
                                            foreach ($runs as $run) {
                                                //Modification de tous les fieldsdata et persist
                                                $fieldata = $run->getDataFromField($field);
                                                $fieldata->setData($data);
                                                $this->entityManager->persist($fieldata);
                                            }
                                        }
                                    }
                                    $options = array_merge($options, [$pointer => trim(htmlspecialchars($data))]);
                                    $pointer++;
                                    break;

                                case 'label':
                                    if ($data === "") {
                                        return new JsonResponse(['message' => "Please fill the name of the list"], 400);
                                    }
                                    $field->addToConfig(['label' => trim(htmlspecialchars($data))]);
                                    break;
                                case 'subcategory':
                                    $field->addToConfig(['subCategory' => true]);
                                    break;
                            }
                        }

                        //Au moins 2 options dans la liste
                        if ($pointer < 2) {
                            return new JsonResponse(['message' => 'Please set at least 2 options in the list'], 400);
                        }

                        $field->addToConfig(['options' => $options]);

                        //Verif sur le type
                        $type = $this->entityManager->getRepository(FieldTypes::class)->findOneBy(['backName' => $fieldType]);
                        if (!$type instanceof FieldTypes) {
                            return new JsonResponse(['message' => 'Invalid field type'], 400);
                        }
                        $field->setRefFieldTypes($type);

                        $field->setRankOrder(1);

                        $this->entityManager->persist($field);
                        break;
                }

            }
            //Un champ doit être "primary" obligatoirement
            if (!$hasPrimary) {
                return new JsonResponse(['message' => 'Please add/select a default timing method'], 400);
            }
            $this->entityManager->flush();
        }else{
            return new JsonResponse(['message' => 'Please add some field'], 400);
        }

        return new JsonResponse(['message' => $categories->getName() . ($create ? ' created' : ' updated') , 'redirect' => $this->generateUrl('app_categories_index',['rewrite' => $categories->getRefGames()->getRewrite()])]);

    }

    public function getFieldsFromCategory(Categories $category): JsonResponse
    {
        $fields = '';

        for ($pointer = 0; $pointer < $category->getPlayers(); $pointer++) {
            $fields .= $this->renderView('runs/includes/load/user.html.twig', [
                'pointer' => $pointer
            ]);
        }

        //[times => [ tempaltes à la suite ] ]
        foreach ($category->getRefFields() as $key => $field) {

            //TODO VOIR POUR LE TRI
            switch ($field->getRefFieldTypes()->getBackName()) {
                case 'time-goal':

                    $fields .= $this->renderView('runs/includes/load/timeGoal.html.twig', [
                        'key' => $key,
                        'fields' => $field
                    ]);

                    break;
                case 'select':
                    $fields .= $this->renderView('runs/includes/load/select.html.twig', [
                        'key' => $key,
                        'fields' => $field
                    ]);
                    break;
            }

            //$this->renderView() RENVOIE DU HTML
            //TODO RENDER TWIG POUR JUSTE AVOIR LE CHAMPS EN HTML
            /*$config = $field->getConfig();
            $config['type'] = $field->getRefFieldTypes()->getBackName();
            $config['id'] = $field->getId();
            $fields[] = $config;*/
        }

        //Ajout du nombre de champs joueurs à charger
        /*$fields[] = ['type' => 'players' , 'number' => $category->getPlayers()];*/

        return new JsonResponse($fields);
    }

    public function loadSubCategories(Categories $categories): JsonResponse
    {

        //GetSubcategories
        $subCategories = $categories->getSubCategories();

        return new JsonResponse($this->renderView('categories/includes/subCategories.html.twig', [
            'subCategories' => $subCategories,
            'category' => $categories,
        ]));

    }

    public function displayRunsByOptions(Categories $categories, array $options = null)
    {

        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('refStatus', $this->entityManager->getRepository(Status::class)->find($options['status'])));
        $runs = $categories->getRefRuns()->matching($criteria);
        $runs = $runs->toArray();

        //sort avec boucle options1 options2
        $subCategories = $categories->getSubCategories();


        $runs = array_filter($runs, function ($run) use ($options, $subCategories) {

            //Tous les champs doivent matcher pour être dans le tableau filter
            foreach ($subCategories as $subCategory) {
                //Cas de modif de catégorie
                if ($run->getDataFromField($subCategory) == null){
                    return false;
                }
                if ($run->getDataFromField($subCategory)->getData() != $options[$subCategory->getConfig()['label']]) {
                    return false;
                }
            }
            return true;
        });

        $runs = $this->sortRunsByComparisons($runs);

        //Renvoie le template avec les infos
        $primaryComparisonField = $categories->getPrimaryComparison();

        $secondaryComparisonField = $categories->getSecondaryComparison();

        //Si aucune comparaison est en place
        if (!$primaryComparisonField instanceof Fields) {
            return new JsonResponse(['message' => 'Wrong category configuration'], 400);
        }

        //Cas où aucune subCategories à afficher donc affichage des runs


        $configFields = $categories->getConfigLeaderboard();


        return new JsonResponse($this->renderView('categories/includes/leaderboard.html.twig', [
            'runs' => $runs,
            'primaryComparison' => $primaryComparisonField,
            'secondaryComparison' => $secondaryComparisonField,
            'subCategories' => $categories->getSubCategories(),
            'configFields' => $configFields,
            'category' => $categories,
        ]));
    }

    public function sortRunsByComparisons(array $runs): array
    {
        //TODO FAIRE NUMBER ?

        if (!empty($runs)) {

            $categories = reset($runs)->getRefCategories();

            $primaryComparisonField = $categories->getPrimaryComparison();

            $secondaryComparisonField = $categories->getSecondaryComparison();

            usort($runs, function ($a, $b) use ($primaryComparisonField, $secondaryComparisonField) {
                $primaryA = $a->getPrimaryComparisonData($primaryComparisonField)->getData();
                $primaryB = $b->getPrimaryComparisonData($primaryComparisonField)->getData();


                if ($primaryA == $primaryB) {
                    //Cas où pas de secondary
                    if ($secondaryComparisonField === null) {
                        return 0;
                    }
                    $secondaryA = $a->getSecondaryComparisonData($secondaryComparisonField);
                    $secondaryB = $b->getSecondaryComparisonData($secondaryComparisonField);

                    if ($secondaryA === null && $secondaryB === null) {
                        return 0;
                    }elseif($secondaryA === null){
                        return 1;
                    }elseif($secondaryB === null){
                        return -1;
                    }

                    $secondaryAData = $secondaryA->getData();
                    $secondaryBData = $secondaryB->getData();

                    if ($secondaryAData === null && $secondaryBData === null) {
                        return 0;
                    } elseif ($secondaryAData === null) {
                        return 1;
                    } elseif ($secondaryBData === null) {
                        return -1;
                    } else {
                        return $secondaryAData <=> $secondaryBData;
                    }
                }

                return $primaryA <=> $primaryB;
            });
        }

        return $runs;
    }
}
