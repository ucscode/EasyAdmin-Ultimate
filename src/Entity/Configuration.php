<?php

namespace App\Entity;

use App\Bundle\Abstract\AbstractMetaEntity;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
#[UniqueEntity(fields: 'metaKey')]
class Configuration extends AbstractMetaEntity
{
}
