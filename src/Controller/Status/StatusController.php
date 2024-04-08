<?php

namespace App\Controller\Status;

use App\Entity\Status\Status;
use App\Form\Status\StatusType;
use App\Repository\Status\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/status/status')]
class StatusController extends AbstractController
{
    #[Route('/', name: 'app_status_status_index', methods: ['GET'])]
    public function index(StatusRepository $statusRepository): Response
    {
        return $this->render('status/status/index.html.twig', [
            'statuses' => $statusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_status_status_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = new Status();
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($status);
            $entityManager->flush();

            return $this->redirectToRoute('app_status_status_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('status/status/new.html.twig', [
            'status' => $status,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_status_status_show', methods: ['GET'])]
    public function show(Status $status): Response
    {
        return $this->render('status/status/show.html.twig', [
            'status' => $status,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_status_status_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Status $status, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_status_status_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('status/status/edit.html.twig', [
            'status' => $status,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_status_status_delete', methods: ['POST'])]
    public function delete(Request $request, Status $status, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$status->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($status);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_status_status_index', [], Response::HTTP_SEE_OTHER);
    }
}
