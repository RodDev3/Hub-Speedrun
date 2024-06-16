<?php

namespace App\Controller\Games;

use App\Entity\Games\Games;
use App\Entity\Users\Users;
use App\Form\Games\Games1Type;
use App\Form\Games\GamesType;
use App\Repository\Games\GamesRepository;
use App\Service\BootService\BootService;
use App\Service\Security\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Twig\Environment;

#[Route('/games')]
class GamesController extends AbstractController
{

    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $entityManager,
        private BootService       $bootService,
    )
    {
        new SecurityService($this->twig, $this->entityManager);
    }


    #[Route('/', name: 'app_games_index', methods: ['GET'])]
    public function index(GamesRepository $gamesRepository): Response
    {
        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('games/index.html.twig', [
            'games' => $gamesRepository->findAll(),
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                'Games List' => $this->generateUrl('app_games_index'),
            ],
        ]);
    }

    #[Route('/new', name: 'app_games_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('app_home');
        }

        $game = new Games();
        $form = $this->createForm(GamesType::class, $game);

        return $this->render('games/new.html.twig', [
            'game' => $game,
            'form' => $form,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                'Games List' => $this->generateUrl('app_games_index'),
                'New Game' => $this->generateUrl('app_games_new'),
            ],
        ]);
    }

    #[Route('/{rewrite}', name: 'app_games_show', methods: ['GET'])]
    public function show(Games $game): Response
    {

        /** @var Users $user */
        $user =$this->getUser();

        return $this->render('games/show.html.twig', [
            'game' => $game,
            'user' => $user,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                $game->getName() => $this->generateUrl('app_games_show', ['rewrite' => $game->getRewrite()])
            ],
        ]);
    }

    #[Route('/{uuid}/edit', name: 'app_games_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Games $game, EntityManagerInterface $entityManager): Response
    {
        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            if ($user->getModerationRolesFromGames($game) !== null) {
                if ($user->getModerationRolesFromGames($game)->getRankOrder() > ROLE_MOD_RANK) {
                    return $this->redirectToRoute('app_home');
                }
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        $form = $this->createForm(GamesType::class, $game);
        //$form->handleRequest($request);

        return $this->render('games/edit.html.twig', [
            'game' => $game,
            'form' => $form,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                'Games List' => $this->generateUrl('app_games_index'),
                'Update ' . $game->getName() => $this->generateUrl('app_games_edit' ,['uuid' => $game->getUuid()]),
            ],
        ]);
    }

}
