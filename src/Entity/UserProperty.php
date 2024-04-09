<?php

namespace App\Entity;

use App\Entity\Abstract\AbstractMetaEntity;
use App\Repository\UserPropertyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserPropertyRepository::class)]
#[UniqueEntity(fields: ['user', 'metaKey'])]
class UserProperty extends AbstractMetaEntity
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $epoch = null;

    #[ORM\ManyToOne(inversedBy: 'userProperties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;
    
    public function __construct(?string $key = null, mixed $value = null)
    {
        parent::__construct($key, $value);
        $this->setEpoch(new \DateTime());
    }

    public function getEpoch(): ?\DateTimeInterface
    {
        return $this->epoch;
    }

    public function setEpoch(\DateTimeInterface $epoch): static
    {
        $this->epoch = $epoch;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
