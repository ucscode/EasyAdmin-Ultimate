<?php

namespace App\Entity;

use App\Entity\Abstracts\AbstractEntityMeta;
use App\Repository\UserPropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserPropertyRepository::class)]
#[UniqueEntity(fields: ['user', 'metaKey'])]
class UserProperty extends AbstractEntityMeta
{
    #[ORM\ManyToOne(inversedBy: 'userProperties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

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
