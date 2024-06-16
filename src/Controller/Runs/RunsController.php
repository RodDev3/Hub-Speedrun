<?php

namespace App\Controller\Runs;

use App\Entity\Games\Games;
use App\Entity\Runs\Runs;
use App\Entity\Status\Status;
use App\Entity\Users\Users;
use App\Form\Runs\RunsSubmitType;
use App\Form\Runs\RunsType;
use App\Repository\Runs\RunsRepository;
use App\Service\BootService\BootService;
use App\Service\Security\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[Route('/{rewrite}/runs')]
class RunsController extends AbstractController
{
    public function __construct(
        private readonly Environment            $twig,
        private readonly EntityManagerInterface $entityManager,
        private BootService                     $bootService,
    )
    {
        new SecurityService($this->twig, $this->entityManager);
    }

    #[Route('/verifications', name: 'app_runs_verif_list', methods: ['GET'])]
    public function verificationsIndex(Games $game): Response
    {
        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            //Not admin
            if ($user->getModerationRolesFromGames($game) !== null) {
                //Has Role
                if ($user->getModerationRolesFromGames($game)->getRankOrder() > ROLE_VERIF_RANK) {
                    return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
                }
            } else {
                return $this->redirectToRoute('app_games_show', ['rewrite' => $game->getRewrite()]);
            }
        }

        $runs = $this->entityManager->getRepository(Runs::class)->findBy(['refStatus' => 1], ['dateSubmitted' => 'ASC']);


        //Cas Update de subCategorie
        foreach ($game->getRefCategories() as $category) {
            $allRunsCategory = $this->entityManager->getRepository(Runs::class)->findBy(['refCategories' => $category->getId(), 'refStatus' => $this->entityManager->getRepository(Status::class)->find(2)]);
            foreach ($allRunsCategory as $run) {
                foreach ($category->getSubCategories() as $subCategory) {
                    if($run->getDataFromField($subCategory) === null){
                        //Mets les runs en top de l'affichage
                        array_unshift($runs, $run);
                    }
                }
            }
        }

        return $this->render('runs/index.html.twig', [
            'runs' => $runs,
            'game' => $game,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                $game->getName() => $this->generateUrl('app_games_show', ['rewrite' => $game->getRewrite()]),
                'Runs Verification' => $this->generateUrl('app_runs_verif_list', ['rewrite' => $game->getRewrite()]),
            ],
        ]);
    }

    /*#[Route('/', name: 'app_runs_index', methods: ['GET'])]
    public function index(RunsRepository $runsRepository): Response
    {
        return $this->render('runs/index.html.twig', [
            'runs' => $runsRepository->findAll(),
        ]);
    }*/

    #[Route('/submit', name: 'app_runs_submit', methods: ['GET'])]
    public function submit(Games $game, Request $request, EntityManagerInterface $entityManager): Response
    {
        $run = new Runs();

        $form = $this->createForm(RunsType::class, $run, ['game' => $game]);


        return $this->render('runs/new.html.twig', [
            'game' => $game,
            'user' => $this->getUser(),
            'run' => $run,
            'form' => $form,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                $game->getName() => $this->generateUrl('app_games_show', ['rewrite' => $game->getRewrite()]),
                'Submit Run' => $this->generateUrl('app_runs_submit', ['rewrite' => $game->getRewrite()]),
            ],
        ]);
    }

    #[Route('/{uuid}', name: 'app_runs_show', methods: ['GET'])]
    public function show(Runs $run): Response
    {

        $typeLink = null;

        if (preg_match('/youtube\.com|youtu\.be/', $run->getVideo())) {
            $typeLink = 'youtube';
        } elseif (preg_match('/twitch\.tv/', $run->getVideo())) {
            $typeLink = 'twitch';
        }

        //Data pour le breadcrumb
        $primaryField = $run->getRefCategories()->getPrimaryComparison();
        $primaryData = $run->getPrimaryComparisonData($primaryField);

        return $this->render('runs/show.html.twig', [
            'typeLink' => $typeLink,
            'run' => $run,
            'user' => $this->getUser(),
            'game' => $run->getRefGame(),
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                $run->getRefGame()->getName() => $this->generateUrl('app_games_show', ['rewrite' => $run->getRefGame()->getRewrite()]),
                $run->getRefCategories()->getName() . ' in ' . $run->formatTiming($primaryData->getData()) => $this->generateUrl('app_runs_show', ['rewrite' => $run->getRefGame()->getRewrite() , 'uuid' => $run->getUuid()]),
            ],
        ]);
    }

    #[Route('/verifications/{uuid}', name: 'app_runs_verification', methods: ['GET'])]
    public function verificationShow(Runs $run): Response
    {

        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_games_show', ['rewrite' => $run->getRefGame()->getRewrite()]);
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            //Not admin
            if ($user->getModerationRolesFromGames($run->getRefGame()) !== null) {
                //Has Role
                if ($user->getModerationRolesFromGames($run->getRefGame())->getRankOrder() > ROLE_VERIF_RANK) {
                    return $this->redirectToRoute('app_games_show', ['rewrite' => $run->getRefGame()->getRewrite()]);
                }
            } else {
                return $this->redirectToRoute('app_games_show', ['rewrite' => $run->getRefGame()->getRewrite()]);
            }
        }

        $typeLink = null;

        if (preg_match('/youtube\.com|youtu\.be/', $run->getVideo())) {
            $typeLink = 'youtube';
        } elseif (preg_match('/twitch\.tv/', $run->getVideo())) {
            $typeLink = 'twitch';
        }

        $form = $this->createForm(RunsType::class, $run, ['game' => $run->getRefGame(), 'uuid' => $run->getUuid()]);


        //Data pour le breadcrumb
        $primaryField = $run->getRefCategories()->getPrimaryComparison();
        $primaryData = $run->getPrimaryComparisonData($primaryField);

        return $this->render('runs/verification.html.twig', [
            'form' => $form->createView(),
            'typeLink' => $typeLink,
            'run' => $run,
            'user' => $this->getUser(),
            'game' => $run->getRefGame(),
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                $run->getRefGame()->getName() => $this->generateUrl('app_games_show', ['rewrite' => $run->getRefGame()->getRewrite()]),
                $run->getRefCategories()->getName() . ' in ' . $run->formatTiming($primaryData->getData()) => $this->generateUrl('app_runs_verification', ['rewrite' => $run->getRefGame()->getRewrite() , 'uuid' => $run->getUuid()]),
            ],
        ]);
    }

    #[Route('/{id}/edit', name: 'app_runs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Runs $run, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RunsType::class, $run);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_runs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('runs/edit.html.twig', [
            'run' => $run,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_runs_delete', methods: ['POST'])]
    public function delete(Request $request, Runs $run, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $run->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($run);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_runs_index', [], Response::HTTP_SEE_OTHER);
    }
}
