<?php

namespace App\Controller\Categories;

use App\Entity\Categories\Categories;
use App\Entity\FieldTypes\FieldTypes;
use App\Entity\Games\Games;
use App\Form\Categories\CategoriesType;
use App\Service\Categories\CategoriesService;
use App\Service\Fields\FieldsService;
use App\Service\Games\GamesService;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/categories')]
class CategoriesAjaxController extends AbstractController
{

    public function __construct(
        private CategoriesService $categoriesService,
        private GamesService      $gamesService,
        private FieldsService     $fieldsService,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/call/addField', name: 'app_categories_call_addField', methods: ['POST'])]
    public function callAddField(Request $request): Response
    {
        $data = json_decode($request->getContent(),true)['type'];

        $fieldType = $this->entityManager->getRepository(FieldTypes::class)->findOneBy(['backName' => $data]);
        if (!$fieldType instanceof FieldTypes){
            return new JsonResponse(['message' => 'Invalid field type'], 400);
        }

        return $this->fieldsService->getNewFields($fieldType);

    }

    #[Route('/call/submit/{uuid}', name: 'app_categories_call_submit', methods: ['POST'])]
    public function callSubmit(Request $request, string $uuid): Response
    {

        $requestCategories = $request->request->all('categories');

        $game = $this->gamesService->getGameFromUuid($requestCategories['refGames']);

        //Si l'uuid a été modifié alors renvoie d'erreur
        if (!$game instanceof Games){
            return new JsonResponse(['message'=> 'An error occurred'], 400);
        }

        if ($uuid == 'null'){
            //Create
            $category = new Categories();
        }else{
            //Edit
            $category = $this->entityManager->getRepository(Categories::class)->findOneBy(['uuid' => $uuid ]);
        }
        if (!$category instanceof Categories){
            //Error while Edit
            return new JsonResponse(['message' => 'An error occurred while retrieving the category'], 400);
        }

        $categoryClone = clone  $category;

        $form = $this->createForm(CategoriesType::class, $category, ['game' => $game] );
        $form->handleRequest($request);

        if ($uuid != 'null'){
            $category->setPlayers($categoryClone->getPlayers());
        }

        //Set le Game car non mapped
        $category->setRefGames($game);

        return $this->categoriesService->categoriesValidation($request,$category);
    }

    #[Route('/call/subCategories/runs', name: 'app_subCategories_call_runs', methods: ['POST'])]
    public function callSubCategoriesRuns(Request $request): Response
    {
        $data = json_decode($request->getContent(),true);

        if ($data['subCategory'] === null){

            //Check if category is valid
            $categories = $this->entityManager->getRepository(Categories::class)->findOneBy(['uuid'=>$data['uuid']]);
            if (!$categories instanceof Categories) {
                return new JsonResponse(['message'=> 'Invalid category'], 400);
            }

            $subCategories = $categories->getSubCategories();
            $options = [];

            //build options[]
            foreach ($subCategories as $subCategory){
                $options = array_merge($options, [$subCategory->getConfig()["label"] => $subCategory->getConfig()["options"][0]]);
            }
            $options = array_merge($options, ['status' => 2]);

            return $this->categoriesService->displayRunsByOptions($categories,$options);

        }else{

            //Check if category is valid
            $categories = $this->entityManager->getRepository(Categories::class)->findOneBy(['uuid'=>$data['uuid']]);
            if (!$categories instanceof Categories) {
                return new JsonResponse(['message'=> 'Invalid category'], 400);
            }

            $options = $data['subCategory'];
            $options = array_merge($options, ['status' => 2]);

            return $this->categoriesService->displayRunsByOptions($categories,$options);
        }
    }


}
