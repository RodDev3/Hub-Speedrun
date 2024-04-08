<?php

namespace App\Entity\FieldData;

use App\Entity\Fields\Fields;
use App\Entity\Runs\Runs;
use App\Repository\FieldData\FieldDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FieldDataRepository::class)]
class FieldData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $data = null;

    #[ORM\ManyToOne(inversedBy: 'refFieldData')]
    private ?Runs $refRuns = null;

    #[ORM\ManyToOne(inversedBy: 'refFieldData')]
    private ?Fields $refFields = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getRefRuns(): ?Runs
    {
        return $this->refRuns;
    }

    public function setRefRuns(?Runs $refRuns): static
    {
        $this->refRuns = $refRuns;

        return $this;
    }

    public function getRefFields(): ?Fields
    {
        return $this->refFields;
    }

    public function setRefFields(?Fields $refFields): static
    {
        $this->refFields = $refFields;

        return $this;
    }
}
