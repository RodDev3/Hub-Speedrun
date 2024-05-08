<?php

namespace App\Controller\Categories;

use App\Entity\Categories\Categories;
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
    ) {}

    #[Route('/call/submit', name: 'app_categories_call_submit', methods: ['GET', 'POST'])]
    public function callSubmit(Request $request): Response
    {
        $requestCategories = $request->request->all('categories');

        $game = $this->gamesService->getGameFromUuid($requestCategories['refGames']);

        //Si l'uuid a été modifié alors renvoie d'erreur
        if (!$game instanceof Games){
            return new JsonResponse(['message'=> 'An error occurred'], 400);
        }

        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category, ['game' => $game] );
        $form->handleRequest($request);

        //Set le Game car non mapped
        $category->setRefGames($game);

        return $this->categoriesService->categoriesValidation($request,$category);
    }
}
