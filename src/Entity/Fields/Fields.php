<?php

namespace App\Entity\Fields;

use App\Entity\Categories\Categories;
use App\Entity\FieldData\FieldData;
use App\Entity\FieldTypes\FieldTypes;
use App\Repository\Fields\FieldsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FieldsRepository::class)]
class Fields
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $display = null;

    #[ORM\Column]
    private ?bool $quickFilter = null;

    #[ORM\Column]
    private array $config = [];

    #[ORM\ManyToOne(inversedBy: 'refFields')]
    private ?Categories $refCategories = null;

    #[ORM\ManyToOne(inversedBy: 'refFields')]
    private ?FieldTypes $refFieldTypes = null;

    #[ORM\OneToMany(targetEntity: FieldData::class, mappedBy: 'refFields')]
    private Collection $refFieldData;

    #[ORM\Column]
    private ?int $rankOrder = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    public function __construct()
    {
        $this->refFieldData = new ArrayCollection();
        $this->uuid = Uuid::v4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isDisplay(): ?bool
    {
        return $this->display;
    }

    public function setDisplay(bool $display): static
    {
        $this->display = $display;

        return $this;
    }

    public function isQuickFilter(): ?bool
    {
        return $this->quickFilter;
    }

    public function setQuickFilter(bool $quickFilter): static
    {
        $this->quickFilter = $quickFilter;

        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): static
    {
        $this->config = $config;

        return $this;
    }

    public function addToConfig(array $array): static
    {
        $configOld = $this->getConfig();
        $configNew = array_merge($configOld, $array) ;
        return $this->setConfig($configNew);
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

    public function getRefFieldTypes(): ?FieldTypes
    {
        return $this->refFieldTypes;
    }

    public function setRefFieldTypes(?FieldTypes $refFieldTypes): static
    {
        $this->refFieldTypes = $refFieldTypes;

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
            $refFieldData->setRefFields($this);
        }

        return $this;
    }

    public function removeRefFieldData(FieldData $refFieldData): static
    {
        if ($this->refFieldData->removeElement($refFieldData)) {
            // set the owning side to null (unless already changed)
            if ($refFieldData->getRefFields() === $this) {
                $refFieldData->setRefFields(null);
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

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }
}
