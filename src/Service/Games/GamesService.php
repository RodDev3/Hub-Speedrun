<?php

namespace App\Service\Games;

use App\Entity\Games\Games;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class GamesService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function getGameFromRewrite(string $rewrite): Games
    {
        return $this->entityManager->getRepository(Games::class)->findOneBy(['rewrite' => $rewrite]);
    }

    public function getGameFromUuid(string $uuid): ?Games
    {
        return $this->entityManager->getRepository(Games::class)->findOneBy(['uuid' => $uuid]);
    }

}