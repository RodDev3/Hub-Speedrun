<?php

namespace App\Controller\Runs;

use App\Entity\Categories\Categories;
use App\Entity\Games\Games;
use App\Entity\Runs\Runs;
use App\Form\Runs\RunsType;
use App\Repository\Runs\RunsRepository;
use App\Service\Categories\CategoriesService;
use App\Service\Runs\RunsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/runs')]
class RunsAjaxController extends AbstractController
{
    public function __construct(
        private CategoriesService $categoriesService,
        private EntityManagerInterface $entityManager,
        private RunsService $runsService
    ) {}

    #[Route('/fields/call', name: 'app_runs_fields_call', methods: ['POST'])]
    public function loadFieldsCall(Request $request): Response
    {
        $category = $this->entityManager->getRepository(Categories::class)->findOneBy(['uuid' => $request->request->all()['runs']['refCategories']]);

        if (!$category instanceof Categories){
            return new JsonResponse(['message' => 'An error occurred'], 400);
        }

        $game = $this->entityManager->getRepository(Games::class)->findOneBy(['uuid' => $request->request->all()['runs']['game']]);
        if (!$game instanceof Games){
            return new JsonResponse(['message' => 'An error occurred'], 400);
        }

        //Gestion d'erreur si le user change l'uuid par un autre correspondant
        //TODO Voir comment faire si l'on change l'uuid du game aussi car ça charge les field
        //Ok ça affiche le mauvais form mais à la validation il y a peu voir pas de chance que ça tombe pas en erreur
        if ($category->getRefGames() !== $game){
            return new JsonResponse(['message' => 'An error occurred'], 400);
        }

        return $this->categoriesService->getFieldsFromCategory($category);
        //Return les fields en fonction de la category donc category->getFields ?
        /*return $this->categoriesService->getFieldsFromCategory()*/
    }
    #[Route('/submit/call', name: 'app_runs_submit_call', methods: ['POST'])]
    public function submitCall(Request $request): Response
    {

        $game = $this->entityManager->getRepository(Games::class)->findOneBy(['uuid' => $request->request->all()['runs']['game']]);
        if (!$game instanceof Games){
            return new JsonResponse(['An error occurred'], 400);
        }

        $run = new Runs();
        $form = $this->createForm(RunsType::class, $run, ['game' => $game]);
        $form->handleRequest($request);

        return $this->runsService->runsValidation($run, $request);
    }
}