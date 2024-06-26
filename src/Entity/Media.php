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
    public const TYPE_APPLICATION = 'application';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_IMAGE = 'image';
    public const TYPE_MESSAGE = 'message';
    public const TYPE_MULTIPART = 'multipart';
    public const TYPE_TEXT = 'text';
    public const TYPE_VIDEO = 'video';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(
        mapping: 'media',
        fileNameProperty: 'embeddedFile.name',
        size: 'embeddedFile.size',
        mimeType: 'embeddedFile.mimeType',
        originalName: 'embeddedFile.originalName',
        dimensions: 'embeddedFile.dimensions'
    )]
    private ?UploadedFile $uploadedFile = null;

    #[ORM\Embedded(class: EmbeddedFile::class)]
    private ?EmbeddedFile $embeddedFile = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    public function __construct()
    {
        $this->embeddedFile = new EmbeddedFile();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
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

    public function getMimeParts(?int $index): array|string|null
    {
        if($mimeType = $this->getEmbeddedFile()->getMimeType()) {
            $mimeParts = explode("/", $mimeType);
            return $index !== null ? ($mimeParts[$index] ?? null) : $mimeParts;
        }

        return [];
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
}
