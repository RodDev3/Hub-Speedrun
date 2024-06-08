<?php

namespace App\Controller\Users;

use App\Entity\Users\Users;
use App\Form\Users\UsersType;
use App\Repository\Users\UsersRepository;
use App\Service\Categories\CategoriesService;
use App\Service\Runs\RunsService;
use App\Service\Users\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
class UsersAjaxController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UsersService $usersService
    ) {}

    #[Route('/new/call', name: 'app_user_new_call', methods: ['POST'])]
    public function submitNewUser(Request $request): JsonResponse
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        return $this->usersService->newSubmit($user);
    }
}
