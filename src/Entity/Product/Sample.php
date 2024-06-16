<?php

namespace App\Entity\Product;

use App\Entity\Contract\Product;
use App\Repository\Product\SampleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SampleRepository::class)]
class Sample extends Product
{
    // Define custom properties here...
}
