<?php

namespace App\Controller\Games;

use App\Entity\Games\Games;
use App\Form\Games\Games1Type;
use App\Form\Games\GamesType;
use App\Repository\Games\GamesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/games')]
class GamesController extends AbstractController
{
    #[Route('/', name: 'app_games_index', methods: ['GET'])]
    public function index(GamesRepository $gamesRepository): Response
    {
        return $this->render('games/index.html.twig', [
            'games' => $gamesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_games_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Games();
        $form = $this->createForm(GamesType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $game->setUuid(Uuid::v4());
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('games/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{rewrite}', name: 'app_games_show', methods: ['GET'])]
    public function show(Games $game): Response
    {
        return $this->render('games/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_games_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Games $game, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GamesType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('games/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_games_games_delete', methods: ['POST'])]
    public function delete(Request $request, Games $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
    }
}
