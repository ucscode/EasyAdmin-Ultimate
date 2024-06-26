<?php

namespace App\Entity\Contract;

use App\Entity\Media;
use App\Entity\Product\Sample;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Unless you are assured that all entities extending Product will contain the same attribute, do not set #[ORM\Column(nullable: true)].
 * Instead, use an assertion constraint for entity-specific validation.
 *
 * Example:
 *
 * #[ORM\OneToOne(cascade: ['persist', 'remove'])]
 * #[ORM\JoinColumn(nullable: true)]
 * #[Assert\NotNull]
 * private ?Property $property = null;
 */

#[ORM\Entity]
#[InheritanceType(value: "SINGLE_TABLE")]
#[DiscriminatorColumn(name: "__discr__", type: "string")]
#[DiscriminatorMap(value: [
    Sample::IDENTIFIER => Sample::class,
    // add more product classes here
])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    protected ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    protected ?string $description = null;

    #[ORM\Column]
    protected ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    protected ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 20)]
    protected ?string $status = null;

    #[ORM\Column]
    protected ?int $originalPrice = 0;

    #[ORM\Column]
    protected ?int $salePrice = 0;

    #[ORM\Column(length: 20, unique: true)]
    protected ?string $sku = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\ManyToMany(targetEntity: Media::class)]
    private Collection $images;
    
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSalePrice(): ?int
    {
        return $this->salePrice;
    }

    public function setSalePrice(int $price): static
    {
        $this->salePrice = $price;

        return $this;
    }

    public function getOriginalPrice(): ?int
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(int $originalPrice): static
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Media $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
        }

        return $this;
    }

    public function removeImage(Media $image): static
    {
        $this->images->removeElement($image);

        return $this;
    }
}
