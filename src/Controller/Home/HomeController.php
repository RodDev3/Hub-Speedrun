<?php

namespace App\Controller\Home;

use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_home')]
    public function index(): Response
    {
        $search = $this->createForm(SearchType::class);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'searchBar' => $search->createView()
        ]);
    }
}
