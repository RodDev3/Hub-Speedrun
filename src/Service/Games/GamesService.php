<?php

namespace App\Service\Games;

use App\Entity\Games\Games;
use Doctrine\ORM\EntityManagerInterface;

class GamesService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function getGameFromRewrite(string $rewrite): Games
    {
        return $this->entityManager->getRepository(Games::class)->findOneBy(['rewrite' => $rewrite]);
    }
}