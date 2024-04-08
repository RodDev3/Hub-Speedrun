<?php

namespace App\Entity\Runs;

use App\Entity\Categories\Categories;
use App\Entity\FieldData\FieldData;
use App\Entity\Status\Status;
use App\Entity\Users\Users;
use App\Repository\Runs\RunsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RunsRepository::class)]
class Runs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $uniqueId = null;

    #[ORM\ManyToOne(inversedBy: 'refRuns')]
    private ?Categories $refCategories = null;

    #[ORM\ManyToOne(inversedBy: 'refRuns')]
    private ?Status $refStatus = null;

    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'refRuns')]
    private Collection $refUsers;

    #[ORM\OneToMany(targetEntity: FieldData::class, mappedBy: 'refRuns')]
    private Collection $refFieldData;

    public function __construct()
    {
        $this->refUsers = new ArrayCollection();
        $this->refFieldData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniqueId(): ?int
    {
        return $this->uniqueId;
    }

    public function setUniqueId(int $uniqueId): static
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    public function getRefCategories(): ?Categories
    {
        return $this->refCategories;
    }

    public function setRefCategories(?Categories $refCategories): static
    {
        $this->refCategories = $refCategories;

        return $this;
    }

    public function getRefStatus(): ?Status
    {
        return $this->refStatus;
    }

    public function setRefStatus(?Status $refStatus): static
    {
        $this->refStatus = $refStatus;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getRefUsers(): Collection
    {
        return $this->refUsers;
    }

    public function addRefUser(Users $refUser): static
    {
        if (!$this->refUsers->contains($refUser)) {
            $this->refUsers->add($refUser);
        }

        return $this;
    }

    public function removeRefUser(Users $refUser): static
    {
        $this->refUsers->removeElement($refUser);

        return $this;
    }

    /**
     * @return Collection<int, FieldData>
     */
    public function getRefFieldData(): Collection
    {
        return $this->refFieldData;
    }

    public function addRefFieldData(FieldData $refFieldData): static
    {
        if (!$this->refFieldData->contains($refFieldData)) {
            $this->refFieldData->add($refFieldData);
            $refFieldData->setRefRuns($this);
        }

        return $this;
    }

    public function removeRefFieldData(FieldData $refFieldData): static
    {
        if ($this->refFieldData->removeElement($refFieldData)) {
            // set the owning side to null (unless already changed)
            if ($refFieldData->getRefRuns() === $this) {
                $refFieldData->setRefRuns(null);
            }
        }

        return $this;
    }
}