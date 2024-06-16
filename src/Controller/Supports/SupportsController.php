<?php

namespace App\Controller\Supports;

use App\Entity\Supports\Supports;
use App\Entity\Users\Users;
use App\Form\Supports\SupportsType;
use App\Repository\Supports\SupportsRepository;
use App\Service\BootService\BootService;
use App\Service\Security\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/supports')]
class SupportsController extends AbstractController
{
    public function __construct(
        BootService $bootService,
        SecurityService $securityService
    ) {

    }
    #[Route('/', name: 'app_supports_index', methods: ['GET'])]
    public function index(SupportsRepository $supportsRepository): Response
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

        return $this->render('supports/index.html.twig', [
            'supports' => $supportsRepository->findAll(),
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                'Supports List' => $this->generateUrl('app_supports_index'),
            ],
        ]);
    }

    #[Route('/new', name: 'app_supports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
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

        $support = new Supports();
        $form = $this->createForm(SupportsType::class, $support);
        $form->handleRequest($request);

        return $this->render('supports/new.html.twig', [
            'support' => $support,
            'form' => $form,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                'Supports List' => $this->generateUrl('app_supports_index'),
                'New Support' => $this->generateUrl('app_supports_new'),
            ],
        ]);
    }

    #[Route('/{id}/edit', name: 'app_supports_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Supports $support, EntityManagerInterface $entityManager): Response
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

        $form = $this->createForm(SupportsType::class, $support);
        $form->handleRequest($request);


        return $this->render('supports/edit.html.twig', [
            'support' => $support,
            'form' => $form,
            'breadcrumb' => [
                'Home' => $this->generateUrl('app_home'),
                'Supports List' => $this->generateUrl('app_supports_index'),
                'Edit ' . $support->getName() => $this->generateUrl('app_supports_edit', ['id' => $support->getId()]),
            ],
        ]);
    }
}
