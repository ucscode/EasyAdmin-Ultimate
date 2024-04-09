<?php

namespace App\Entity;

use App\Immutable\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_UUID', fields: ['uuid'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $uuid = null;

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

    #[ORM\Column(length: 255)]
    private ?string $referralCode = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastSeen = null;

    /**
     * @var Collection<int, UserMeta>
     */
    #[ORM\OneToMany(targetEntity: UserMeta::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $metaCollection;

    /**
     * @var Collection<int, UserNotification>
     */
    #[ORM\OneToMany(targetEntity: UserNotification::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $notificationCollection;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    public function __construct()
    {
        $this->uuid = Uuid::v4()->toRfc4122();
        $this->roles[] = UserRole::ROLE_USER;
        $this->referralCode = explode("-", $this->uuid)[0];
        $this->registrationTime = new \DateTimeImmutable();
        $this->lastSeen = new \DateTimeImmutable();
        $this->metaCollection = new ArrayCollection();
        $this->notificationCollection = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->uuid;
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

    public function setPassword(?string $plainPassword, bool $hashPassword = true): static
    {
        $this->password = $plainPassword && $hashPassword ? 
            $this->getPasswordHasher()->hashPassword($this, $plainPassword) :
            $plainPassword;

        return $this;
    }

    public function isPasswordValid(string $plainPassword): bool
    {
        return $this->getPasswordHasher()->isPasswordValid($this, $plainPassword);
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

    public function getReferralCode(): ?string
    {
        return $this->referralCode;
    }

    public function setReferralCode(string $referralCode): static
    {
        $this->referralCode = $referralCode;

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
     * @return Collection<int, UserMeta>
     */
    public function getMetaCollection(): Collection
    {
        return $this->metaCollection;
    }
    
    public function getMetaByKey(string $key): ?UserMeta
    {
        foreach ($this->metaCollection as $meta) {
            if ($meta->getKey() === $key) {
                return $meta;
            }
        }
        return null;
    }

    public function addMeta(UserMeta $meta): static
    {
        if (!$this->metaCollection->contains($meta)) {
            $meta->setUser($this);
            $this->metaCollection->add($meta);
        }

        return $this;
    }

    public function removeMeta(UserMeta $meta): static
    {
        if ($this->metaCollection->removeElement($meta)) {
            // set the owning side to null (unless already changed)
            if ($meta->getUser() === $this) {
                $meta->setUser(null);
            }
        }

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

    private function getPasswordHasher(): UserPasswordHasher
    {

        $passwordHasherFactory = new PasswordHasherFactory([
            self::class => ['algorithm' => 'auto'],
            PasswordAuthenticatedUserInterface::class => [
                'algorithm' => 'auto',
                'cost' => 15,
            ],
        ]);
        return new UserPasswordHasher($passwordHasherFactory);
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
}
