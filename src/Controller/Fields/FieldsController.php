<?php

namespace App\Controller\Fields;

use App\Entity\Categories\Categories;
use App\Entity\Fields\Fields;
use App\Form\Fields\FieldsType;
use App\Repository\Fields\FieldsRepository;
use App\Service\Fields\FieldsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('categories/{idCategories}/fields')]
class FieldsController extends AbstractController
{

    public function __construct(
        private FieldsService $fieldsService,
        private EntityManagerInterface $entityManager,
    ){}

    #[Route('/', name: 'app_fields_index', methods: ['GET'])]
    public function index(int $idCategories): Response
    {

        return $this->render('fields/index.html.twig', [
            'fields' => $this->fieldsService->getFieldsFromCategories($idCategories),
            'categories' => $this->entityManager->getRepository(Categories::class)->findOneBy(['id' => $idCategories])
        ]);
    }

    #[Route('/new', name: 'app_fields_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $field = new Fields();
        $form = $this->createForm(FieldsType::class, $field);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($field);
            $entityManager->flush();

            return $this->redirectToRoute('app_fields_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fields/new.html.twig', [
            'field' => $field,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fields_show', methods: ['GET'])]
    public function show(Fields $field): Response
    {
        return $this->render('fields/show.html.twig', [
            'field' => $field,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fields_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fields $field, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FieldsType::class, $field);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fields_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fields/edit.html.twig', [
            'field' => $field,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fields_fields_delete', methods: ['POST'])]
    public function delete(Request $request, Fields $field, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$field->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($field);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fields_index', [], Response::HTTP_SEE_OTHER);
    }
}
