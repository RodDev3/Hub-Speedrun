<?php

namespace App\Service\Categories;

use App\Entity\Categories\Categories;
use App\Entity\Fields\Fields;
use App\Entity\FieldTypes\FieldTypes;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\New_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class CategoriesService extends AbstractController
{


    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}


    public function getCategoriesFromRewrite(string $rewrite): array
    {
        return $this->entityManager->getRepository(Categories::class)->getCategoriesFromRewrite($rewrite);
    }

    public function categoriesValidation(Request $request, Categories $categories)
    {

        $formData = $request->request->all();

        //TODO VOIR SI JE PASSE SUR DU VALIDATOR

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

        $categories->setUuid(Uuid::v4());
        //Persist/flush la caté
        $this->entityManager->persist($categories);



        //Vérification s'il y a bien des champs ajoutés
        if (isset ($formData['categories']['fields'])) {

            $hasPrimary = false;
            $hasSecondary = false;

            //Vérification des données sur chaque nouveau champ
            foreach ($formData['categories']['fields'] as $key => $fieldData) {

                $field = new Fields();
                $field->setRefCategories($categories);

                $explodedType = explode('.', $key);
                $fieldType = $explodedType[0];


                //TODO COMPLETER AVEC TOUS LES TYPES DE CHAMPS
                switch ($fieldType) {

                    case 'time-goal':
                        //TODO BOUGER CA DANS CATEGORIE =>
                        //TODO primaryTiming !nullable ref vers fields
                        //TODO secondaryTiming nullable ref vers fields
                        //TODO ON GARDE LES GOALS (NOM A MODIFIER) pour les IGT non secondaire (IGT différent mais same place)
                        //TODO à l'affichage du form dans la partie "Timing ou autre" afficher primaryTiming, secondary Timing et les autres qui n'affecte pas le classement

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

                        //Vérif sur le type de champ
                        $type = $this->entityManager->getRepository(FieldTypes::class)->findOneBy(['backName' => $fieldType]);
                        if (!$type instanceof FieldTypes) {
                            return new JsonResponse(['message' => 'Invalid field type'], 400);
                        }
                        $field->setRefFieldTypes($type);

                        //TODO RETIRER CA JUSTE POUR TEST
                        $field->setDisplay(true);
                        $field->setQuickFilter(true);
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
                                    if ($data === ''){
                                        return new JsonResponse(['message' => "Please fill all options in the list"], 400);
                                    }

                                    $options = array_merge($options, [$pointer => trim(htmlspecialchars($data))]);
                                    $pointer++;
                                    break;
                                case 'label':
                                    if ($data === ""){
                                        return new JsonResponse(['message' => "Please fill the name of the list"], 400);
                                    }
                                    $field->addToConfig(['label' => trim(htmlspecialchars($data))]);
                                    break;
                            }
                        }

                        //Au moins 2 options dans la liste
                        if ($pointer < 2){
                            return new JsonResponse(['message' => 'Please set at least 2 options in the list'], 400);
                        }

                        $field->addToConfig(['options' => $options]);

                        //Verif sur le type
                        $type = $this->entityManager->getRepository(FieldTypes::class)->findOneBy(['backName' => $fieldType]);
                        if (!$type instanceof FieldTypes) {
                            return new JsonResponse(['message' => 'Invalid field type'], 400);
                        }
                        $field->setRefFieldTypes($type);

                        //TODO RETIRER CA JUSTE POUR TEST
                        $field->setDisplay(true);
                        $field->setQuickFilter(true);
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
        }


        return new JsonResponse(['message' => 'Everything is good']);

        //TODO REDIRECT ?

        /*return $this->redirectToRoute('app_categories_index', ['rewrite' => $rewrite], Response::HTTP_SEE_OTHER);*/

        //TODO !!!!!!!!!!!!!!!!!!! UNE FOIS FINI ICI IL FAUT AFFICHER LE FORMULAIRE ET INSERER DES RUNS POUR LE LEADERBOARD
    }

    public function getFieldsFromCategory(Categories $category): JsonResponse
    {
        $fields = '';

        for ($pointer = 0; $pointer < $category->getPlayers(); $pointer++) {
            $fields .= $this->renderView('runs/includes/load/user.html.twig', [
                'pointer' => $pointer
            ]);
        }

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
}
