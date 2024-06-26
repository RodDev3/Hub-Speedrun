<?php

namespace App\Entity\Roles;

use App\Entity\Games\Games;
use App\Entity\Moderations\Moderations;
use App\Repository\Roles\RolesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolesRepository::class)]
class Roles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Moderations::class, mappedBy: 'refRoles')]
    private Collection $refModerations;

    #[ORM\Column]
    private ?int $rankOrder = null;

    #[ORM\Column(length: 255)]
    private ?string $keyName = null;

    public function __construct()
    {
        $this->refModerations = new ArrayCollection();
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
            $refModeration->setRefRoles($this);
        }

        return $this;
    }

    public function removeRefModeration(Moderations $refModeration): static
    {
        if ($this->refModerations->removeElement($refModeration)) {
            // set the owning side to null (unless already changed)
            if ($refModeration->getRefRoles() === $this) {
                $refModeration->setRefRoles(null);
            }
        }

        return $this;
    }

    public function getRankOrder(): ?int
    {
        return $this->rankOrder;
    }

    public function setRankOrder(int $rankOrder): static
    {
        $this->rankOrder = $rankOrder;

        return $this;
    }

    public function getKeyName(): ?string
    {
        return $this->keyName;
    }

    public function setKeyName(string $keyName): static
    {
        $this->keyName = $keyName;

        return $this;
    }

    public function getUsersFromRolesAndGames(Games $games): Collection
    {
        $criteria = Criteria::create();
        $criteria
            ->andWhere(criteria::expr()->eq('refRoles', $this))
            ->andWhere(criteria::expr()->eq('refGames', $games))
        ;

         return $this->getRefModerations()->matching($criteria);
    }
}
