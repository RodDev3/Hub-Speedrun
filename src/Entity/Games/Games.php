<?php

namespace App\Entity\Games;

use App\Entity\Categories\Categories;
use App\Entity\Moderations\Moderations;
use App\Entity\Series\Series;
use App\Entity\Supports\Supports;
use App\Repository\Games\GamesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamesRepository::class)]
class Games
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(length: 255)]
    private ?string $discordLink = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $rewrite = null;

    #[ORM\ManyToMany(targetEntity: Supports::class, mappedBy: 'refGames')]
    private Collection $refSupports;

    #[ORM\ManyToOne(inversedBy: 'refGames')]
    private ?Series $refSeries = null;

    #[ORM\OneToMany(targetEntity: Moderations::class, mappedBy: 'refGames')]
    private Collection $refModerations;

    #[ORM\OneToMany(targetEntity: Categories::class, mappedBy: 'refGames')]
    private Collection $refCategories;

    public function __construct()
    {
        $this->refSupports = new ArrayCollection();
        $this->refModerations = new ArrayCollection();
        $this->refCategories = new ArrayCollection();
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

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getDiscordLink(): ?string
    {
        return $this->discordLink;
    }

    public function setDiscordLink(string $discordLink): static
    {
        $this->discordLink = $discordLink;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getRewrite(): ?string
    {
        return $this->rewrite;
    }

    public function setRewrite(string $rewrite): static
    {
        $this->rewrite = $rewrite;

        return $this;
    }

    /**
     * @return Collection<int, Supports>
     */
    public function getSupports(): Collection
    {
        return $this->refSupports;
    }

    public function addRefSupport(Supports $support): static
    {
        if (!$this->refSupports->contains($support)) {
            $this->refSupports->add($support);
            $support->addRefGame($this);
        }

        return $this;
    }

    public function removeRefSupport(Supports $support): static
    {
        if ($this->refSupports->removeElement($support)) {
            $support->removeRefGame($this);
        }

        return $this;
    }

    public function getRefSeries(): ?Series
    {
        return $this->refSeries;
    }

    public function setRefSeries(?Series $refSeries): static
    {
        $this->refSeries = $refSeries;

        return $this;
    }

    /**
     * @return Collection<int, Moderations>
     */
    public function getRefModerations(): Collection
    {
        return $this->refModerations;
    }

    public function addRefModeration(Moderations $refModeration): static
    {
        if (!$this->refModerations->contains($refModeration)) {
            $this->refModerations->add($refModeration);
            $refModeration->setRefGames($this);
        }

        return $this;
    }

    public function removeRefModeration(Moderations $refModeration): static
    {
        if ($this->refModerations->removeElement($refModeration)) {
            // set the owning side to null (unless already changed)
            if ($refModeration->getRefGames() === $this) {
                $refModeration->setRefGames(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Categories>
     */
    public function getRefCategories(): Collection
    {
        return $this->refCategories;
    }

    public function addRefCategory(Categories $refCategory): static
    {
        if (!$this->refCategories->contains($refCategory)) {
            $this->refCategories->add($refCategory);
            $refCategory->setRefGames($this);
        }

        return $this;
    }

    public function removeRefCategory(Categories $refCategory): static
    {
        if ($this->refCategories->removeElement($refCategory)) {
            // set the owning side to null (unless already changed)
            if ($refCategory->getRefGames() === $this) {
                $refCategory->setRefGames(null);
            }
        }

        return $this;
    }
}
