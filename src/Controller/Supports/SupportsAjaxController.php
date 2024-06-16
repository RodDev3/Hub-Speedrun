<?php

namespace App\Controller\Supports;

use App\Entity\Supports\Supports;

use App\Form\Supports\SupportsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SupportsAjaxController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {

    }

    #[Route('/supports/call/{id}', name: 'app_supports_call', methods: ['POST'])]
    public function supportsCall(Request $request, string $id): JsonResponse
    {

        if ($id == 'null'){
            $create = true;
            $support = new Supports();
        }else{
            $create = false;
            $support = $this->entityManager->getRepository(Supports::class)->find($id);
            if (!$support instanceof Supports) {
                return new JsonResponse(['message' => 'Invalid support'], 400);
            }
        }


        $form = $this->createForm(SupportsType::class, $support);
        $form->handleRequest($request);

        if ($support->getName() === null) {
            return new JsonResponse(['message' => 'Please fill the support name'], 400);
        }

        $this->entityManager->persist($support);

        $this->entityManager->flush();

        return new JsonResponse(['message' =>  ($create ? $support->getName() . ' created successfully' : $support->getName() . ' updated successfully'), 'redirect' => $this->generateUrl('app_supports_index')], 200);
    }
}