<?php

namespace App\Entity\FieldTypes;

use App\Entity\Fields\Fields;
use App\Repository\FieldTypes\FieldTypesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FieldTypesRepository::class)]
class FieldTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $displayName = null;

    #[ORM\Column(length: 255)]
    private ?string $backName = null;

    #[ORM\OneToMany(targetEntity: Fields::class, mappedBy: 'refFieldTypes')]
    private Collection $refFields;


    public function __construct()
    {
        $this->refFields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBackName(): ?string
    {
        return $this->backName;
    }

    public function setBackName(string $name): static
    {
        $this->backName = $name;

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
            $refField->setRefFieldTypes($this);
        }

        return $this;
    }

    public function removeRefField(Fields $refField): static
    {
        if ($this->refFields->removeElement($refField)) {
            // set the owning side to null (unless already changed)
            if ($refField->getRefFieldTypes() === $this) {
                $refField->setRefFieldTypes(null);
            }
        }

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }
}
