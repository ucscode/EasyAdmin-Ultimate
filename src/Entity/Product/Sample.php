<?php

namespace App\Entity\Product;

use App\Entity\Contract\Product;
use App\Repository\Product\SampleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SampleRepository::class)]
class Sample extends Product
{
    public const IDENTIFIER = 'sample';

    // Define custom properties

    public function __toString()
    {
        return $this->title;
    }

    // Define setters & getters
}
