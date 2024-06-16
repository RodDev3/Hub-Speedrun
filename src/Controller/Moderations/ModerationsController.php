<?php

namespace App\Controller\Moderations;

use App\Entity\Games\Games;
use App\Entity\Moderations\Moderations;
use App\Entity\Roles\Roles;
use App\Entity\Users\Users;
use App\Form\Moderations\ModerationsType;
use App\Repository\Moderations\ModerationsRepository;
use App\Service\BootService\BootService;
use App\Service\Security\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;
use function PHPUnit\Framework\identicalTo;

#[Route('/{rewrite}/moderations')]
class ModerationsController extends AbstractController
{
    public function __construct(
        private readonly Environment            $twig,
        private readonly EntityManagerInterface $entityManager,
        private BootService                     $bootService
    )
    {
        new SecurityService($this->twig, $this->entityManager);
    }

    #[Route('/', name: 'app_moderations_index', methods: ['GET'])]
    public function index(Games $game): Response
    {

        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            if ($user->getModerationRolesFromGames($game) !== null) {
                if ($user->getModerationRolesFromGames($game)->getRankOrder() > ROLE_SMOD_RANK) {
                    return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
                }
            } else {
                return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
            }
        }

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
            "game" => $game,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                $game->getName() => $this->generateUrl('app_games_show', ['rewrite' => $game->getRewrite()]),
                'Moderation' => $this->generateUrl('app_moderations_index', ['rewrite' => $game->getRewrite()])
            ],
        ]);
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
        if ($this->isCsrfTokenValid('delete' . $moderation->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($moderation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_moderations_moderations_index', [], Response::HTTP_SEE_OTHER);
    }
}
