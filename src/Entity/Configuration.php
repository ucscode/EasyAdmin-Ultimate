<?php

namespace App\Entity;

use App\Entity\Abstract\MetaEntity;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Configuration extends MetaEntity
{
}
