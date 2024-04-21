<?php

namespace App\Service\Search;

use App\Entity\Games\Games;
use App\Entity\Users\Users;
use Doctrine\ORM\EntityManagerInterface;

class SearchService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function getResultFromSearch(string $search): Games
    {
        $games = $this->entityManager->getRepository(Games::class)->getGamesFromResearch($search);
        $players = $this->entityManager->getRepository(Users::class)->getPlayersFromResearch($search);

        dd($games, $players);
    }
}