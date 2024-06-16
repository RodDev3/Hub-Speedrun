<?php

namespace App\Controller\Games;

use App\Entity\Categories\Categories;
use App\Entity\Games\Games;
use App\Form\Games\GamesType;
use App\Service\Categories\CategoriesService;
use App\Service\Games\GamesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use function PHPUnit\Framework\isEmpty;

class GamesAjaxController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoriesService      $categoriesService, private readonly GamesService $gamesService
    ) {}

    #[Route('/game/call/categories', name: 'app_games_call_categories', methods: ['GET', 'POST'])]
    public function callCategories(Request $request): JsonResponse
    {

        $categories = $this->entityManager->getRepository(Categories::class)->findOneBy(['uuid' => json_decode($request->getContent(), true)['categories']]);
        if (!$categories instanceof Categories) {
            return new JsonResponse(['message' => 'Invalid Category'], 400);
        }

        $games = $this->entityManager->getRepository(Games::class)->findOneBy(['rewrite' => json_decode($request->getContent(), true)['games']]);
        if (!$games instanceof Games) {
            return new JsonResponse(['message' => 'Invalid Game'], 400);
        }

        if (!$games->getRefCategories()->contains($categories)) {
            return new JsonResponse(['message' => 'Invalid Game/Category'], 400);
        }

        return $this->categoriesService->loadSubCategories($categories);

    }

    #[Route('/call/form/{uuid}', name: 'app_form_call_game', methods: ['GET', 'POST'])]
    public function callGames(Request $request, string $uuid): JsonResponse
    {

        if ($uuid == 'null'){
            $create = true;
            $game = new Games();
        }else{
            $create = false;
            $game = $this->entityManager->getRepository(Games::class)->findOneBy(['uuid' => $uuid]);
            if (!$game instanceof Games) {
                return new JsonResponse(['message' => 'Invalid game'], 400);
            }
        }


        $form = $this->createForm(GamesType::class, $game);
        $form->handleRequest($request);


        if ($game->getName() === null) {
            return new JsonResponse(['message' => 'Please fill the game name'], 400);
        }

        $game->setRewrite($this->gamesService->sanitizeRewrite($game->getName()));

        if ($game->getImage() === null){
            return new JsonResponse(['message' => 'Please fill the game image'], 400);
        }
        if ($game->getImage() === null) {
            return new JsonResponse(['message' => 'Please fill the game released date'], 400);
        }
        if ($game->getRefSupports()->count() === 0) {
            return new JsonResponse(['message' => 'Please fill the game support'], 400);
        }

        $game->setActive(true);
        $game->setUuid(Uuid::v4());


        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return new JsonResponse(['message' =>  ($create ? $game->getName() . ' created successfully' : $game->getName() . ' updated successfully'), 'redirect' => $this->generateUrl('app_games_index')], 200);
    }
}