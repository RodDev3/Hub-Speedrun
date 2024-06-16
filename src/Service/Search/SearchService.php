<?php

namespace App\Service\Search;

use App\Entity\Games\Games;
use App\Entity\Users\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SearchService extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function getResultFromSearch(string $search): JsonResponse
    {

        if (trim(htmlspecialchars($search)) === ""){
            return new JsonResponse([
                'status' => Response::HTTP_OK,
                'players' => "",
                'games' => ""
            ]);
        }

        /* @var Games[] $games */
        $games = $this->entityManager->getRepository(Games::class)->getGamesFromResearch(htmlspecialchars($search));

        /* @var Users[] $players */
        $players = $this->entityManager->getRepository(Users::class)->getPlayersFromResearch(htmlspecialchars($search));


        $arrayGames = [];
        foreach ($games as $key => $game){
            $arrayGames[$key]['name'] = $game->getName();
            $arrayGames[$key]['rewrite'] = $game->getRewrite();
            $arrayGames[$key]['image'] = $game->getImage();
            $arrayGames[$key]['releaseDate'] = $game->getReleaseDate()->format('Y');
        }

        $arrayPlayers = [];
        foreach ($players as $key => $player){
            $arrayPlayers[$key]['name'] = $player->getUsername();
            //TODO LE REWRITE ET PEUT ETRE ICON/LOGO
            /*$arrayPlayers[$key]['rewrite'] = $players->getRewrite();*/
        }

        return new JsonResponse([
            'status' => Response::HTTP_OK,
            'players' => $arrayPlayers,
            'games' => $this->renderView('/search/games.html.twig', [
                'games' => $arrayGames,
            ]),
        ]);
    }
}