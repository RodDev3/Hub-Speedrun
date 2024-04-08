<?php

namespace App\Entity\Supports;

use App\Entity\Games\Games;
use App\Repository\Supports\SupportsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupportsRepository::class)]
class Supports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Games::class, inversedBy: 'supports')]
    private Collection $refGames;

    public function __construct()
    {
        $this->refGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Games>
     */
    public function getRefGames(): Collection
    {
        return $this->refGames;
    }

    public function addRefGame(Games $refGame): static
    {
        if (!$this->refGames->contains($refGame)) {
            $this->refGames->add($refGame);
        }

        return $this;
    }

    public function removeRefGame(Games $refGame): static
    {
        $this->refGames->removeElement($refGame);

        return $this;
    }
}
