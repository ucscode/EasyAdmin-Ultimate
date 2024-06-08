<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Vich\Uploadable]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(
        mapping: 'media'
    )]
    private ?UploadedFile $uploadedFile = null;

    #[ORM\Embedded(class: EmbeddedFile::class)]
    private ?EmbeddedFile $embeddedFile = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadedFile(): ?UploadedFile
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(?UploadedFile $uploadedFile): static
    {
        $this->uploadedFile = $uploadedFile;

        if($this->uploadedFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getEmbeddedFile(): ?EmbeddedFile
    {
        return $this->embeddedFile;
    }

    public function setEmbeddedFile(?EmbeddedFile $embeddedFile): static
    {
        $this->embeddedFile = $embeddedFile;

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
}
