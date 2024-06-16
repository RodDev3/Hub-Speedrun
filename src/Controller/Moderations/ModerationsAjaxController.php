<?php

namespace App\Controller\Moderations;

use App\Entity\Games\Games;
use App\Entity\Moderations\Moderations;
use App\Entity\Roles\Roles;
use App\Entity\Users\Users;
use App\Form\Moderations\ModerationsType;
use App\Service\Security\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Twig\Environment;

#[Route('/{rewrite}/moderations')]
class ModerationsAjaxController extends AbstractController
{


    public function __construct(
        private readonly Environment            $twig,
        private readonly EntityManagerInterface $entityManager) {
        new SecurityService($this->twig, $this->entityManager);
    }

    #[Route('/call/user', name: 'app_moderations_call_user', methods: ['POST'])]
    public function callManageAdmin(Games $game, Request $request): JsonResponse
    {

        $requestModerations = $request->request->all('moderations');

        $user = $this->entityManager->getRepository(Users::class)->find(
            $requestModerations['refUsers']
        );


        $moderation = $this->entityManager->getRepository(Moderations::class)->findOneBy([
            "refGames" => $game,
            "refUsers" => $user
        ]);


        if ($moderation instanceof Moderations) {
            $moderation->setRefRoles($moderation->getRefRoles());
        } else {
            $moderation = new Moderations();
        }

        $form = $this->createForm(ModerationsType::class, $moderation, ['game' => $game]);

        return new JsonResponse(
            [
                "twigTemplate" => $this->renderView('moderations/ajax.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user,
                    'game' => $game
                ])
            ]
        );
    }

    #[Route('/call/moderation/submit', name: 'app_moderations_call_submit', methods: ['POST'])]
    public function submitModeration(Games $game, Request $request): JsonResponse
    {
        $requestModerations = $request->request->all('moderations');

        $user = $this->entityManager->getRepository(Users::class)->find(
            $requestModerations['refUsers']
        );

        $role = $this->entityManager->getRepository(Roles::class)->find($requestModerations['refRoles']);


        //Verifications
        if ($user == ""){
            return new JsonResponse(['message' => 'Please select a user'], 400);
        }
        if (!$user instanceof Users){
            return new JsonResponse(['message' => 'Invalid User'], 400);
        }

        if (!$game instanceof Games){
            return new JsonResponse(['message' => 'Invalid Game'], 400);
        }


        if ($role == ""){
            return new JsonResponse(['message' => 'Please select a role'], 400);
        }
        if (!$role instanceof Roles){
            return new JsonResponse(['message' => 'Invalid Role'], 400);
        }

        $moderation = $this->entityManager->getRepository(Moderations::class)->findOneBy([
            "refGames" => $game,
            "refUsers" => $user
        ]);

        //Check if user is already in moderation
        if ($moderation instanceof Moderations) {
            $moderation->setRefRoles($role);
        } else {
            $moderation = new Moderations();
            $moderation->setRefGames($game)
                ->setRefRoles($role)
                ->setRefUsers($user)
            ;
        }

        //flush
        $this->entityManager->persist($moderation);
        $this->entityManager->flush();


        //TODO CLOSE POPUP AND REFRESH LIST USERS
        return new JsonResponse(
            [
                "message" => 'Changes applied successfully',
            ]
        );
    }

    #[Route('/call/moderation/delete', name: 'app_moderations_call_delete', methods: ['POST'])]
    public function deleteUserModeration(Games $game, Request $request): JsonResponse
    {
        $requestModerations = $request->request->all('moderations');

        $user = $this->entityManager->getRepository(Users::class)->find(
            $requestModerations['refUsers']
        );

        //Verifications
        if ($user == ""){
            return new JsonResponse(['message' => 'Please select a user'], 400);
        }
        if (!$user instanceof Users){
            return new JsonResponse(['message' => 'Invalid User'], 400);
        }

        if (!$game instanceof Games){
            return new JsonResponse(['message' => 'Invalid Game'], 400);
        }

        $moderation = $this->entityManager->getRepository(Moderations::class)->findOneBy([
            "refGames" => $game,
            "refUsers" => $user
        ]);


        $this->entityManager->remove($moderation);
        $this->entityManager->flush();


        //TODO CLOSE POPUP AND REFRESH LIST USERS
        return new JsonResponse(
            [
                "message" => 'User deleted successfully',
            ]
        );
    }
}