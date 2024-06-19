<?php

namespace App\DataFixtures\Media;

use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mime\MimeTypes;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;

class MediaFixture extends Fixture
{
    public const FILES = [
        'leopard.svg',
        'solar-system.md',
        'summer.mp3',
        'superman.gif',
    ];

    public function __construct(protected KernelInterface $kernel)
    {
    }

    public function load(ObjectManager $manager)
    {
        foreach(self::FILES as $filename) {
            $uploadedFile = $this->getUploadedFile($filename);
            // $embeddedFile = $this->getEmbeddedFile($uploadedFile);
            
            $media = new Media();
            $media->setTitle($uploadedFile->getClientOriginalName());
            $media->setUploadedFile($uploadedFile);
            
            $manager->persist($media);
        }

        $manager->flush();
    }

    protected function getUploadedFile(string $filename): UploadedFile
    {
        $mimeTypesInstance = new MimeTypes();

        $filepath = sprintf("%s/Files/%s", __DIR__, $filename);
        $copypath = sprintf('%s/Copies/%s', __DIR__, $filename);

        is_file($copypath) ?: copy($filepath, $copypath);

        return new UploadedFile(
            $copypath, 
            pathinfo($copypath, PATHINFO_BASENAME),
            $mimeTypesInstance->guessMimeType($copypath),
            null,
            true
        );
    }

    protected function getEmbeddedFile(UploadedFile $file): EmbeddedFile
    {
        $filepath = $file->getFileInfo()->getPathname();
        $filesize = filesize($filepath);
        $imagesize = getimagesize($filepath);

        $embeddedFile = new EmbeddedFile();
        $embeddedFile->setName($file->getClientOriginalName());
        $embeddedFile->setOriginalName($file->getClientOriginalName());
        $embeddedFile->setMimeType($file->getMimeType());
        $embeddedFile->setSize($filesize);

        if($imagesize) {
            $embeddedFile->setDimensions(array_slice($imagesize, 0, 2));
        }

        return $embeddedFile;
    }
}