<?php

namespace App\Entity\Runs;

use App\Entity\Categories\Categories;
use App\Entity\FieldData\FieldData;
use App\Entity\Fields\Fields;
use App\Entity\Games\Games;
use App\Entity\Status\Status;
use App\Entity\Users\Users;
use App\Repository\Runs\RunsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use function PHPUnit\Framework\isEmpty;

#[ORM\Entity(repositoryClass: RunsRepository::class)]
class Runs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'refRuns')]
    private ?Categories $refCategories = null;

    #[ORM\ManyToOne(inversedBy: 'refRuns')]
    private ?Status $refStatus = null;

    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'refRuns')]
    private Collection $refUsers;

    #[ORM\OneToMany(targetEntity: FieldData::class, mappedBy: 'refRuns')]
    private Collection $refFieldData;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSubmitted = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateMade = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $modNotes = null;

    #[ORM\ManyToOne(inversedBy: 'runsVerified')]
    private ?Users $verifiedBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCheck = null;

    public function __construct()
    {
        $this->refUsers = new ArrayCollection();
        $this->refFieldData = new ArrayCollection();
        $this->uuid = Uuid::v4();
        $this->dateMade = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRefGame(): ?Games
    {
        return $this->getRefCategories()->getRefGames();
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

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Collection<int, Fields>
     */
    public function getFields(): Collection
    {
        return $this->getRefCategories()->getRefFields();
    }

    public function getPrimaryComparisonData(Fields $primaryComparisonField)
    {

        foreach ($this->getRefFieldData() as $fieldData) {
            if ($fieldData->getRefFields()->getId() === $primaryComparisonField->getId() && $fieldData->getRefRuns() === $this) {
                return $fieldData;
            }
        }

        return null;
    }

    public function getSecondaryComparisonData(?Fields $secondaryComparisonField)
    {

        /*if ($secondaryComparisonField === null){
            return null;
        }*/
        foreach ($this->getRefFieldData() as $fieldData) {
            if ($fieldData->getRefFields()->getId() === $secondaryComparisonField->getId() && $fieldData->getRefRuns() === $this) {
                return $fieldData;
            }
        }

        return null;
    }

    public function getDataFromField(Fields $fields): FieldData|null
    {

        $criteria = Criteria::create()->andWhere(Criteria::expr()->eq("refFields", $fields));

        if ($this->getRefFieldData()->matching($criteria) !== null) {
            if ($this->getRefFieldData()->matching($criteria)->count() == 0) {
                return null;
            }
            return $this->getRefFieldData()->matching($criteria)->first();
        }

        return null;
    }

    public function getSubCategoriesData(Categories $category): array
    {
        $subCategories = $category->getSubCategories();
        $subCategoriesValues = [];

        foreach ($subCategories as $subCategory) {
            $fielData = $this->getDataFromField($subCategory);
            $subCategoriesValues = array_merge($subCategoriesValues, [$subCategory->getConfig()['label'] => $fielData->getData()]);
        }

        return $subCategoriesValues;

    }

    public function getDateSubmitted(): ?\DateTimeInterface
    {
        return $this->dateSubmitted;
    }

    public function setDateSubmitted(\DateTimeInterface $dateSubmitted): static
    {
        $this->dateSubmitted = $dateSubmitted;

        return $this;
    }

    public function getDateMade(): ?\DateTimeInterface
    {
        return $this->dateMade;
    }

    public function setDateMade(\DateTimeInterface $dateMade): static
    {
        $this->dateMade = $dateMade;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getModNotes(): ?string
    {
        return $this->modNotes;
    }

    public function setModNotes(?string $modNotes): static
    {
        $this->modNotes = $modNotes;

        return $this;
    }

    public function getVerifiedBy(): ?Users
    {
        return $this->verifiedBy;
    }

    public function setVerifiedBy(?Users $verifiedBy): static
    {
        $this->verifiedBy = $verifiedBy;

        return $this;
    }

    public function getDateCheck(): ?\DateTimeInterface
    {
        return $this->dateCheck;
    }

    public function setDateCheck(?\DateTimeInterface $dateCheck): static
    {
        $this->dateCheck = $dateCheck;

        return $this;
    }

    public function formatTiming(string $milliseconds)
    {
        $seconds = floor($milliseconds / 1000);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);

        $seconds = $seconds % 60;
        $minutes = $minutes % 60;
        $milliseconds = $milliseconds % 1000;

        $duration = '';

        if ($hours != 0) {
            $duration .= sprintf('%02dh ', $hours);
        }

        if ($minutes !== 0 || $hours !== 0 || $seconds !== 0) {
            $duration .= sprintf('%02dm ', $minutes);
        }

        if ($seconds !== 0 || $minutes !== 0 || $hours !== 0) {
            $duration .= sprintf('%02ds ', $seconds);
        }

        if ($milliseconds !== 0) {
            $duration .= sprintf('%03dms', $milliseconds);
        }

        return $duration;
    }
}
