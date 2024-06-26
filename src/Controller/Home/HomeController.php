<?php

namespace App\Controller\Home;

use App\Form\Categories\CategoriesType;
use App\Form\SearchType;
use App\Service\BootService\BootService;
use App\Service\Search\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{

    public function __construct(
        private SearchService $searchService,
        private BootService $bootService
    ){}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $search = $this->createForm(SearchType::class);

        return $this->render('home/index.html.twig', [
            'searchBar' => $search->createView(),
            'home' => true
        ]);
    }

    #[Route('/research/call', name: 'app_research_call')]
    public function researchCall(Request $request): JsonResponse
    {
        //Récup value depuis request
        $searchValue = $request->get("search")['search'];

        return $this->searchService->getResultFromSearch($searchValue);

        /*return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'searchBar' => $search->createView()
        ]);*/
    }
}
