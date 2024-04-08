<?php

namespace App\Controller\Series;

use App\Entity\Series\Series;
use App\Form\Series\SeriesType;
use App\Repository\Series\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/series/series')]
class SeriesController extends AbstractController
{
    #[Route('/', name: 'app_series_series_index', methods: ['GET'])]
    public function index(SeriesRepository $seriesRepository): Response
    {
        return $this->render('series/series/index.html.twig', [
            'series' => $seriesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_series_series_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $series = new Series();
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($series);
            $entityManager->flush();

            return $this->redirectToRoute('app_series_series_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('series/series/new.html.twig', [
            'series' => $series,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_series_series_show', methods: ['GET'])]
    public function show(Series $series): Response
    {
        return $this->render('series/series/show.html.twig', [
            'series' => $series,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_series_series_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_series_series_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('series/series/edit.html.twig', [
            'series' => $series,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_series_series_delete', methods: ['POST'])]
    public function delete(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$series->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($series);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_series_series_index', [], Response::HTTP_SEE_OTHER);
    }
}
