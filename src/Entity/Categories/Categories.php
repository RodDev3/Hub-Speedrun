<?php

namespace App\Entity\Categories;

use App\Entity\Fields\Fields;
use App\Entity\Games\Games;
use App\Entity\Runs\Runs;
use App\Repository\Categories\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $rules = null;

    #[ORM\Column]
    private ?int $players = null;

    #[ORM\ManyToOne(inversedBy: 'refCategories')]
    private ?Games $refGames = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'refCategories')]
    private ?self $refCategories = null;

    #[ORM\OneToMany(targetEntity: Fields::class, mappedBy: 'refCategories')]
    private Collection $refFields;

    #[ORM\OneToMany(targetEntity: Runs::class, mappedBy: 'refCategories')]
    private Collection $refRuns;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    public function __construct()
    {
        /*$this->refCategories = new ArrayCollection();*/
        $this->refFields = new ArrayCollection();
        $this->refRuns = new ArrayCollection();
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

    public function getRules(): ?string
    {
        return $this->rules;
    }

    public function setRules(string $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function getPlayers(): ?int
    {
        return $this->players;
    }

    public function setPlayers(int $players): static
    {
        $this->players = $players;

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

    public function getRefCategories(): ?self
    {
        return $this->refCategories;
    }

    public function setRefCategories(?self $refCategories): static
    {
        $this->refCategories = $refCategories;

        return $this;
    }

    public function addRefCategory(self $refCategory): static
    {
        if (!$this->refCategories->contains($refCategory)) {
            $this->refCategories->add($refCategory);
            $refCategory->setRefCategories($this);
        }

        return $this;
    }

    public function removeRefCategory(self $refCategory): static
    {
        if ($this->refCategories->removeElement($refCategory)) {
            // set the owning side to null (unless already changed)
            if ($refCategory->getRefCategories() === $this) {
                $refCategory->setRefCategories(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fields>
     */
    public function getRefFields(): Collection
    {
        return $this->refFields;
    }

    public function addRefField(Fields $refField): static
    {
        if (!$this->refFields->contains($refField)) {
            $this->refFields->add($refField);
            $refField->setRefCategories($this);
        }

        return $this;
    }

    public function removeRefField(Fields $refField): static
    {
        if ($this->refFields->removeElement($refField)) {
            // set the owning side to null (unless already changed)
            if ($refField->getRefCategories() === $this) {
                $refField->setRefCategories(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Runs>
     */
    public function getRefRuns(): Collection
    {
        return $this->refRuns;
    }

    public function addRefRun(Runs $refRun): static
    {
        if (!$this->refRuns->contains($refRun)) {
            $this->refRuns->add($refRun);
            $refRun->setRefCategories($this);
        }

        return $this;
    }

    public function removeRefRun(Runs $refRun): static
    {
        if ($this->refRuns->removeElement($refRun)) {
            // set the owning side to null (unless already changed)
            if ($refRun->getRefCategories() === $this) {
                $refRun->setRefCategories(null);
            }
        }

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    //TODO FAIRE UNE METHOD GET PRIMARY TIME QUI VIENT CHECK TOUT LES FIELDS
}
