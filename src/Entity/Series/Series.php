<?php

namespace App\Entity\Series;

use App\Entity\Games\Games;
use App\Repository\Series\SeriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeriesRepository::class)]
class Series
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Games::class, mappedBy: 'refSeries')]
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
            $refGame->setRefSeries($this);
        }

        return $this;
    }

    public function removeRefGame(Games $refGame): static
    {
        if ($this->refGames->removeElement($refGame)) {
            // set the owning side to null (unless already changed)
            if ($refGame->getRefSeries() === $this) {
                $refGame->setRefSeries(null);
            }
        }

        return $this;
    }
}
