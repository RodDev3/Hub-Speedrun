<?php

namespace App\Entity\Moderations;

use App\Entity\Games\Games;
use App\Entity\Roles\Roles;
use App\Entity\Users\Users;
use App\Repository\Moderations\ModerationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModerationsRepository::class)]
class Moderations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'refModerations')]
    private ?Roles $refRoles = null;

    #[ORM\ManyToOne(inversedBy: 'refModerations')]
    private ?Users $refUsers = null;

    #[ORM\ManyToOne(inversedBy: 'refModerations')]
    private ?Games $refGames = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefRoles(): ?Roles
    {
        return $this->refRoles;
    }

    public function setRefRoles(?Roles $refRoles): static
    {
        $this->refRoles = $refRoles;

        return $this;
    }

    public function getRefUsers(): ?Users
    {
        return $this->refUsers;
    }

    public function setRefUsers(?Users $refUsers): static
    {
        $this->refUsers = $refUsers;

        return $this;
    }

    public function getRefGames(): ?Games
    {
        return $this->refGames;
    }

    public function setRefGames(?Games $refGames): static
    {
        $this->refGames = $refGames;

        return $this;
    }
}
