<?php

namespace App\Entity\Status;

use App\Entity\Runs\Runs;
use App\Repository\Status\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Runs::class, mappedBy: 'refStatus')]
    private Collection $refRuns;

    public function __construct()
    {
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
            $refRun->setRefStatus($this);
        }

        return $this;
    }

    public function removeRefRun(Runs $refRun): static
    {
        if ($this->refRuns->removeElement($refRun)) {
            // set the owning side to null (unless already changed)
            if ($refRun->getRefStatus() === $this) {
                $refRun->setRefStatus(null);
            }
        }

        return $this;
    }
}
