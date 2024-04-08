<?php

namespace App\Controller\Supports;

use App\Entity\Supports\Supports;
use App\Form\Supports\SupportsType;
use App\Repository\Supports\SupportsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/supports')]
class SupportsController extends AbstractController
{
    #[Route('/', name: 'app_supports_supports_index', methods: ['GET'])]
    public function index(SupportsRepository $supportsRepository): Response
    {
        return $this->render('supports/supports/index.html.twig', [
            'supports' => $supportsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_supports_supports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $support = new Supports();
        $form = $this->createForm(SupportsType::class, $support);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($support);
            $entityManager->flush();

            return $this->redirectToRoute('app_supports_supports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('supports/supports/new.html.twig', [
            'support' => $support,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_supports_supports_show', methods: ['GET'])]
    public function show(Supports $support): Response
    {
        return $this->render('supports/supports/show.html.twig', [
            'support' => $support,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_supports_supports_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Supports $support, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SupportsType::class, $support);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_supports_supports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('supports/supports/edit.html.twig', [
            'support' => $support,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_supports_supports_delete', methods: ['POST'])]
    public function delete(Request $request, Supports $support, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$support->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($support);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_supports_supports_index', [], Response::HTTP_SEE_OTHER);
    }
}
