<?php

namespace App\Controller\Moderations;

use App\Entity\Games\Games;
use App\Entity\Moderations\Moderations;
use App\Entity\Roles\Roles;
use App\Entity\Users\Users;
use App\Form\Moderations\ModerationsType;
use App\Repository\Moderations\ModerationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{rewrite}/moderations')]
class ModerationsController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/', name: 'app_moderations_index', methods: ['GET'])]
    public function index(Games $game): Response
    {

        // TODO si game existe pas
        $roles = $this->entityManager->getRepository(Roles::class)->findBy([], ['rankOrder' => 'ASC']);

        //Get users from roles & game
        $moderations = [];
        foreach ($roles as $role) {
            $moderations[] = [
                "role" => $role,
                "users" => $this->entityManager->getRepository(Users::class)->findUsersFromRolesAndGames($role, $game)
            ];
        }


        $moderation = new Moderations();
        $form = $this->createForm(ModerationsType::class, $moderation, ['game' => $game]);

        return $this->render('moderations/index.html.twig', [
            'moderations' => $moderations,
            'form' => $form->createView(),
            "game" => $game
        ]);
    }

    #[Route('/call/user', name: 'app_moderations_call_user', methods: ['POST'])]
    public function callManageUsers(Games $game, Request $request): JsonResponse
    {

        $requestModerations = $request->request->all('moderations');
        /*$request->request->get("moderations")['refUsers']; // users
        $request->request->get("moderations_refgames"); // game*/


        //TODO VErif
        $user = $this->entityManager->getRepository(Users::class)->find(
            $requestModerations['refUsers']
        );


        $moderation = $this->entityManager->getRepository(Moderations::class)->findOneBy([
            "refGames" => $game,
            "refUsers" => $user
        ]);


        if ($moderation instanceof Moderations) {
            $moderation->setRefRoles($moderation->getRefRoles());
        } else {
            $moderation = new Moderations();
        }

        $form = $this->createForm(ModerationsType::class, $moderation, ['game' => $game]);

        return new JsonResponse(
            [
                "twigTemplate" => $this->renderView('moderations/ajax.html.twig', [
                    'form' => $form->createView(),
                ])
            ]
        );
    }



    #[Route('/new', name: 'app_moderations_moderations_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $moderation = new Moderations();
        $form = $this->createForm(ModerationsType::class, $moderation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($moderation);
            $entityManager->flush();

            return $this->redirectToRoute('app_moderations_moderations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('moderations/moderations/new.html.twig', [
            'moderation' => $moderation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_moderations_moderations_show', methods: ['GET'])]
    public function show(Moderations $moderation): Response
    {
        return $this->render('moderations/moderations/show.html.twig', [
            'moderation' => $moderation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_moderations_moderations_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Moderations $moderation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ModerationsType::class, $moderation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_moderations_moderations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('moderations/moderations/edit.html.twig', [
            'moderation' => $moderation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_moderations_moderations_delete', methods: ['POST'])]
    public function delete(Request $request, Moderations $moderation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$moderation->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($moderation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_moderations_moderations_index', [], Response::HTTP_SEE_OTHER);
    }
}
