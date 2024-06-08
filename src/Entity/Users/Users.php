<?php

namespace App\Entity\Users;

use App\Entity\Games\Games;
use App\Entity\Moderations\Moderations;
use App\Entity\Roles\Roles;
use App\Entity\Runs\Runs;
use App\Repository\Users\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Moderations::class, mappedBy: 'refUsers')]
    private Collection $refModerations;

    #[ORM\ManyToMany(targetEntity: Runs::class, mappedBy: 'refUsers')]
    private Collection $refRuns;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function __construct()
    {
        $this->refModerations = new ArrayCollection();
        $this->refRuns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $refModeration->setRefUsers($this);
        }

        return $this;
    }

    public function removeRefModeration(Moderations $refModeration): static
    {
        if ($this->refModerations->removeElement($refModeration)) {
            // set the owning side to null (unless already changed)
            if ($refModeration->getRefUsers() === $this) {
                $refModeration->setRefUsers(null);
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
            $refRun->addRefUser($this);
        }

        return $this;
    }

    public function removeRefRun(Runs $refRun): static
    {
        if ($this->refRuns->removeElement($refRun)) {
            $refRun->removeRefUser($this);
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getModerationRolesFromGames(Games $games): ?Roles
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('refGames', $games));

        $roles = $this->getRefModerations()->matching($criteria);

        return $roles->isEmpty() ? null : $roles->first()->getRefRoles();
    }
}
