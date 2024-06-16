<?php

namespace App\Controller\Users;

use App\Entity\Users\Users;
use App\Form\Users\UpdateUsersType;
use App\Form\Users\UsersType;
use App\Repository\Users\UsersRepository;
use App\Service\Categories\CategoriesService;
use App\Service\Runs\RunsService;
use App\Service\Users\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
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

    #[Route('/update/call/{username}', name: 'app_user_update_call', methods: ['POST'])]
    public function submitUpdateUser(Request $request, string $username): JsonResponse
    {

        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['username' => $username]);
        if (!$user instanceof Users) {
            return new JsonResponse(['error' => 'User not found'], 400);
        }

        return $this->usersService->updateUser($user, $request);
    }

    #[Route('/call/admins/add', name: 'app_user_admin_add_call', methods: ['POST'])]
    public function callManageAdminsAdd(Request $request): JsonResponse
    {

        $requestId = $request->request->all('manage_admins');

        $user = $this->entityManager->getRepository(Users::class)->find(
            $requestId['username']
        );

        if (!$user instanceof Users){
            return new JsonResponse(['message' => 'Invalid User'], 400);
        }

        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'message' => $user->getUsername().' added successfully',
                'redirect' => $this->generateUrl('app_admin_manage')
            ]
        );
    }

    #[Route('/call/admins/delete', name: 'app_user_admin_delete_call', methods: ['POST'])]
    public function callManageAdminsDelete(Request $request): JsonResponse
    {

        $requestId = $request->request->all('manage_admins');

        $user = $this->entityManager->getRepository(Users::class)->find(
            $requestId['username']
        );

        if (!$user instanceof Users){
            return new JsonResponse(['message' => 'Invalid User'], 400);
        }

        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'message' => $user->getUsername().' deleted successfully',
                'redirect' => $this->generateUrl('app_admin_manage')
            ]
        );
    }
}
