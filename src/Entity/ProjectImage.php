<?php

namespace App\Entity;

use App\Entity\Traits\ImageFileTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\PositionTrait;
use App\Manager\AssetsManager;
use App\Repository\ProjectImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProjectImageRepository::class)]
#[Vich\Uploadable]
class ProjectImage extends AbstractBase
{
    use ImageFileTrait;
    use NameTrait;
    use PositionTrait;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'images')]
    private Project $project;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[Assert\Image(maxSize: '10M', mimeTypes: [
        AssetsManager::MIME_IMAGE_JPG_TYPE,
        AssetsManager::MIME_IMAGE_JPEG_TYPE,
        AssetsManager::MIME_IMAGE_PNG_TYPE,
        AssetsManager::MIME_IMAGE_GIF_TYPE,
    ], minWidth: 1200)]
    #[Vich\UploadableField(mapping: 'images', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getName() : self::DEFAULT_NULL_STRING;
    }
}
