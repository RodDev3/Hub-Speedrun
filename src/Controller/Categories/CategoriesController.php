<?php

namespace App\Controller\Categories;

use App\Entity\Categories\Categories;
use App\Entity\Games\Games;
use App\Form\Categories\CategoriesType;
use App\Service\Categories\CategoriesService;
use App\Service\Fields\FieldsService;
use App\Service\Games\GamesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/games/{rewrite}/categories')]
class CategoriesController extends AbstractController
{

    public function __construct(
        private CategoriesService $categoriesService,
        private GamesService      $gamesService,
        private FieldsService     $fieldsService,
    ) {}

    #[Route('/', name: 'app_categories_index', methods: ['GET'])]
    public function index(string $rewrite): Response
    {
        return $this->render('categories/index.html.twig', [
            'categories' => $this->categoriesService->getCategoriesFromRewrite($rewrite),
            'game' => $this->gamesService->getGameFromRewrite($rewrite)
        ]);
    }

    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function new(string $rewrite, Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Categories();
        $category->setRefGames($this->gamesService->getGameFromRewrite($rewrite));

        $form = $this->createForm(CategoriesType::class, $category, ['game' => $this->gamesService->getGameFromRewrite($rewrite)]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoriesService->categoriesValidation($request,$category);
        }


        return $this->render('categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
            'game' => $this->gamesService->getGameFromRewrite($rewrite),
            'fieldTypes' => $this->fieldsService->getAllFieldsTypes(),
        ]);
    }

    #[Route('/{id}', name: 'app_categories_show', methods: ['GET'])]
    public function show(string $rewrite, Categories $category): Response
    {
        return $this->render('categories/show.html.twig', [
            'category' => $category,
            'game' => $this->gamesService->getGameFromRewrite($rewrite)
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(string $rewrite, Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'game' => $this->gamesService->getGameFromRewrite($rewrite)
        ]);
    }

    #[Route('/{id}', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(string $rewrite, Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
    }
}
