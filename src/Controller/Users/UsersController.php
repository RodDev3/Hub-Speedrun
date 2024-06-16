<?php

namespace App\Controller\Users;

use App\Entity\Runs\Runs;
use App\Entity\Status\Status;
use App\Entity\Users\Users;
use App\Form\Users\ManageAdminsType;
use App\Form\Users\UpdateUsersType;
use App\Form\Users\UsersType;
use App\Repository\Users\UsersRepository;
use App\Service\BootService\BootService;
use Couchbase\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
class UsersController extends AbstractController
{
    public function __construct(
        private BootService $bootService,
        private EntityManagerInterface $entityManager,
    )
    {

    }

    /*#[Route('/', name: 'app_users_index', methods: ['GET'])]
    public function index(UsersRepository $usersRepository): Response
    {
        return $this->render('users/index.html.twig', [
            'users' => $usersRepository->findAll(),
        ]);
    }*/

    #[Route('/admin/manage', name: 'app_admin_manage', methods: ['GET', 'POST'])]
    public function manageAdmin(): Response
    {

        //Check user role and redirect if not allowed
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user instanceof Users){
            return $this->redirectToRoute('app_home');
        }
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('app_home');
        }

        $user = new Users();
        $form = $this->createForm(ManageAdminsType::class, $user);

        return $this->render('users/manageAdmin.html.twig', [
            'user' => $user,
            'form' => $form,
            'admins' => $this->entityManager->getRepository(Users::class)->findAdmins(),
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                'Manage Admins' => $this->generateUrl('app_admin_manage'),
            ],
        ]);
    }

    #[Route('/new', name: 'app_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() instanceof Users){
            return $this->redirectToRoute('app_home');
        }
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);

        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                'Register' => $this->generateUrl('app_users_new'),
            ],
        ]);
    }

    #[Route('/{username}', name: 'app_users_show', methods: ['GET'])]
    public function show(Users $user): Response
    {
        //Check user role and redirect if not allowed
        /** @var Users $user */
        $userConnected = $this->getUser();
        if ($userConnected !== $user){
            return $this->redirectToRoute('app_home');
        }


        $runs = $this->entityManager->getRepository(Runs::class)->findByUser($user);

        $runsOrdered = [];
        foreach ($runs as $run){
            $gameName = $run->getRefGame()->getName();
            if (!isset($runsOrdered[$gameName])){
                $runsOrdered[$gameName] = [];
            }
            $runsOrdered[$gameName][] = $run;
        }

        $form = $this->createForm(UpdateUsersType::class, $user);

        return $this->render('users/show.html.twig', [
            'user' => $user,
            'runOrderedInGame' => $runsOrdered,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                'Your profile' => $this->generateUrl('app_users_show', ['username' => $user->getUsername()]),
            ],
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_users_users_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_users_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
