<?php

namespace App\Entity;

use App\Entity\Abstract\AbstractMetaEntity;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Configuration extends AbstractMetaEntity
{
}
