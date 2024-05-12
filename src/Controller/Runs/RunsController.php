<?php

namespace App\Controller\Runs;

use App\Entity\Games\Games;
use App\Entity\Runs\Runs;
use App\Form\Runs\RunsSubmitType;
use App\Form\Runs\RunsType;
use App\Repository\Runs\RunsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/games/{rewrite}/runs')]
class RunsController extends AbstractController
{

    #[Route('/', name: 'app_runs_index', methods: ['GET'])]
    public function index(RunsRepository $runsRepository): Response
    {
        return $this->render('runs/index.html.twig', [
            'runs' => $runsRepository->findAll(),
        ]);
    }

    #[Route('/submit', name: 'app_runs_submit', methods: ['GET'])]
    public function submit(Games $games, Request $request, EntityManagerInterface $entityManager): Response
    {
        $run = new Runs();
        $form = $this->createForm(RunsType::class, $run, ['game' => $games]);


        return $this->render('runs/new.html.twig', [
            'run' => $run,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_runs_show', methods: ['GET'])]
    public function show(Runs $run): Response
    {
        return $this->render('runs/show.html.twig', [
            'run' => $run,
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
        if ($this->isCsrfTokenValid('delete'.$run->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($run);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_runs_index', [], Response::HTTP_SEE_OTHER);
    }
}
