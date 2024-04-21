<?php

namespace App\Controller\Home;

use App\Form\Categories\CategoriesType;
use App\Form\SearchType;
use App\Service\Search\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{

    public function __construct(
        private SearchService $searchService
    ){}

    #[Route('/', name: 'app_home_home')]
    public function index(): Response
    {
        $search = $this->createForm(SearchType::class);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'searchBar' => $search->createView()
        ]);
    }

    #[Route('/research/call', name: 'app_research_call')]
    public function researchCall(Request $request): Response
    {
        //Récup value depuis request
        $searchValue = $request->get("search")['search'];

        $this->searchService->getResultFromSearch($searchValue);

        /*return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'searchBar' => $search->createView()
        ]);*/
    }
}
