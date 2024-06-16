<?php

namespace App\Controller\Categories;

use App\Entity\Categories\Categories;
use App\Entity\Games\Games;
use App\Entity\Users\Users;
use App\Form\Categories\CategoriesType;
use App\Service\BootService\BootService;
use App\Service\Categories\CategoriesService;
use App\Service\Fields\FieldsService;
use App\Service\Games\GamesService;
use App\Service\Security\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/games/{rewrite}/categories')]
class CategoriesController extends AbstractController
{

    public function __construct(
        private CategoriesService $categoriesService,
        private GamesService      $gamesService,
        private FieldsService     $fieldsService,
        private BootService       $bootService,
        private SecurityService   $securityService
    ) {}

    #[Route('/', name: 'app_categories_index', methods: ['GET'])]
    public function index(string $rewrite): Response
    {
        $game = $this->gamesService->getGameFromRewrite($rewrite);

        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            if ($user->getModerationRolesFromGames($game) !== null) {
                if ($user->getModerationRolesFromGames($game)->getRankOrder() > ROLE_MOD_RANK) {
                    return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
                }
            } else {
                return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
            }
        }

        return $this->render('categories/index.html.twig', [
            'categories' => $this->categoriesService->getCategoriesFromRewrite($rewrite),
            'game' => $game,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                $game->getName() => $this->generateUrl('app_games_show', ['rewrite' => $game->getRewrite()]),
                'Categories' => $this->generateUrl('app_categories_index',['rewrite' => $rewrite]),
            ],
        ]);
    }

    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function new(string $rewrite, Request $request, EntityManagerInterface $entityManager): Response
    {
        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            if ($user->getModerationRolesFromGames($game) !== null) {
                if ($user->getModerationRolesFromGames($game)->getRankOrder() > ROLE_MOD_RANK) {
                    return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
                }
            } else {
                return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
            }
        }

        $category = new Categories();
        $category->setRefGames($this->gamesService->getGameFromRewrite($rewrite));

        $form = $this->createForm(CategoriesType::class, $category, ['game' => $this->gamesService->getGameFromRewrite($rewrite)]);
        $form->handleRequest($request);


        return $this->render('categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
            'game' => $this->gamesService->getGameFromRewrite($rewrite),
            'fieldTypes' => $this->fieldsService->getAllFieldsTypes(),
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                $this->gamesService->getGameFromRewrite($rewrite)->getName() => $this->generateUrl('app_games_show', ['rewrite' => $this->gamesService->getGameFromRewrite($rewrite)->getRewrite()]),
                'New Category' => $this->generateUrl('app_categories_new',['rewrite' => $rewrite]),
            ],
        ]);
    }

    #[Route('/{uuid}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(string $rewrite, Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            if ($user->getModerationRolesFromGames($game) !== null) {
                if ($user->getModerationRolesFromGames($game)->getRankOrder() > ROLE_MOD_RANK) {
                    return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
                }
            } else {
                return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
            }
        }

        $game = $this->gamesService->getGameFromRewrite($rewrite);
        $form = $this->createForm(CategoriesType::class, $category, ['game' => $game ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'game' => $game,
            'fieldTypes' => $this->fieldsService->getAllFieldsTypes(),
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                $this->gamesService->getGameFromRewrite($rewrite)->getName() => $this->generateUrl('app_games_show', ['rewrite' => $this->gamesService->getGameFromRewrite($rewrite)->getRewrite()]),
                'Update ' . $category->getName() => $this->generateUrl('app_categories_edit',['rewrite' => $rewrite, 'uuid' => $category->getUuid()]),
            ],
        ]);
    }
}
