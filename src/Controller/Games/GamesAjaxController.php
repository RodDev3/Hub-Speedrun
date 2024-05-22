<?php

namespace App\Controller\Games;

use App\Entity\Categories\Categories;
use App\Entity\Games\Games;
use App\Service\Categories\CategoriesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GamesAjaxController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoriesService $categoriesService
    ){}

    #[Route('/game/call/categories', name: 'app_games_call_categories', methods: ['GET', 'POST'])]
    public function callCategories(Request $request) : JsonResponse
    {

        $categories = $this->entityManager->getRepository(Categories::class)->findOneBy(['uuid' => json_decode($request->getContent(),true)['categories']]);
        if (!$categories instanceof Categories) {
            return new JsonResponse(['message' =>'Invalid Category'], 400);
        }

        $games = $this->entityManager->getRepository(Games::class)->findOneBy(['rewrite' => json_decode($request->getContent(),true)['games']]);
        if (!$games instanceof Games) {
            return new JsonResponse(['message' =>'Invalid Game'], 400);
        }

        if (!$games->getRefCategories()->contains($categories)){
            return  new JsonResponse(['message' =>'Invalid Game/Category'], 400);
        }

        return $this->categoriesService->loadLeaderboard($categories);

    }
}