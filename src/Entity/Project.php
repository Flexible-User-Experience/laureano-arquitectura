<?php

namespace App\Entity;

use App\Entity\Traits\BeginDateTrait;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\EndDateTrait;
use App\Entity\Traits\ImageFileTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\SlugTrait;
use App\Entity\Traits\WebsiteTrait;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity(['name'])]
class Project extends AbstractBase
{
    use BeginDateTrait;
    use DescriptionTrait;
    use EndDateTrait;
    use ImageFileTrait;
    use NameTrait;
    use PositionTrait;
    use SlugTrait;
    use WebsiteTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $name;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $beginDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[Vich\UploadableField(mapping: 'projects', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $showInFrontend = true;

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function isShowInFrontend(): bool
    {
        return $this->showInFrontend;
    }

    public function setShowInFrontend(bool $showInFrontend): self
    {
        $this->showInFrontend = $showInFrontend;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getName() : self::DEFAULT_NULL_STRING;
    }
}
