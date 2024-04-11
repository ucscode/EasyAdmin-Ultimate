<?php

namespace App\Entity;

use App\Immutable\UserRole;
use App\Repository\UserRepository;
use App\Service\PrimaryTaskService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_UNIQUE_ID', fields: ['uniqueId'])]
#[UniqueEntity(fields: ['email'])]
#[UniqueEntity(fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $uniqueId = null;

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

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastSeen = null;

    /**
     * @var Collection<int, UserNotification>
     */
    #[ORM\OneToMany(targetEntity: UserNotification::class, mappedBy: 'user', orphanRemoval: true, cascade: ['persist'])]
    private Collection $notificationCollection;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    /**
     * @var Collection<int, UserProperty>
     */
    #[ORM\OneToMany(targetEntity: UserProperty::class, mappedBy: 'user', orphanRemoval: true, cascade: ['persist'])]
    private Collection $userProperties;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $parent = null;

    public function __construct()
    {
        $this->notificationCollection = new ArrayCollection();
        $this->userProperties = new ArrayCollection();

        $this->setUniqueId((new PrimaryTaskService())->keygen(7));
        $this->addRole(UserRole::ROLE_USER);
        $this->setRegistrationTime(new \DateTimeImmutable());
        $this->setLastSeen(new \DateTimeImmutable());
    }

    public function __toString()
    {
        return $this->getEmail();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }

    public function setUniqueId(string $uniqueId): static
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->uniqueId;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

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

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function addRole(string $role): static
    {
        $this->hasRole($role) ?: $this->roles[] = $role;

        return $this;
    }

    public function removeRole(string $role): static
    {
        if($this->hasRole($role)) {
            $key = array_search($role, $this->roles, true);
            if($key !== false) {
                unset($this->roles[$key]);
                $this->roles = array_values($this->roles);
            }
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getRegistrationTime(): ?\DateTimeInterface
    {
        return $this->registrationTime;
    }

    public function setRegistrationTime(\DateTimeInterface $registrationTime): static
    {
        $this->registrationTime = $registrationTime;

        return $this;
    }

    public function getLastSeen(): ?\DateTimeInterface
    {
        return $this->lastSeen;
    }

    public function setLastSeen(?\DateTimeInterface $lastSeen): static
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    /**
     * @return Collection<int, UserNotification>
     */
    public function getNotificationCollection(): Collection
    {
        return $this->notificationCollection;
    }

    public function addNotification(UserNotification $userNotification): static
    {
        if (!$this->notificationCollection->contains($userNotification)) {
            $this->notificationCollection->add($userNotification);
            $userNotification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(UserNotification $userNotification): static
    {
        if ($this->notificationCollection->removeElement($userNotification)) {
            // set the owning side to null (unless already changed)
            if ($userNotification->getUser() === $this) {
                $userNotification->setUser(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, UserProperty>
     */
    public function getUserProperties(): Collection
    {
        return $this->userProperties;
    }

    public function addUserProperty(UserProperty $userProperty): static
    {
        if (!$this->userProperties->contains($userProperty)) {
            $this->userProperties->add($userProperty);
            $userProperty->setUser($this);
        }

        return $this;
    }

    public function removeUserProperty(UserProperty $userProperty): static
    {
        if ($this->userProperties->removeElement($userProperty)) {
            // set the owning side to null (unless already changed)
            if ($userProperty->getUser() === $this) {
                $userProperty->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }
}
