<?php

namespace App\Entity;

use App\Entity\Traits\BeginDateTrait;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\EndDateTrait;
use App\Entity\Traits\ImageFileTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\SlugTrait;
use App\Entity\Translations\ProjectTranslation;
use App\Manager\AssetsManager;
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

#[Gedmo\TranslationEntity(class: ProjectTranslation::class)]
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

    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'object', targetEntity: ProjectTranslation::class, cascade: ['persist', 'remove'])]
    private ?Collection $translations;

    #[Assert\Count(min: 1)]
    #[Assert\Valid]
    #[ORM\ManyToMany(targetEntity: ProjectCategory::class)]
    private ?Collection $projectCategories;

    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ProjectImage::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private ?Collection $images;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $name;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $slug;

    #[Gedmo\Translatable]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $summary = null;

    #[Gedmo\Translatable]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $beginDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $showInFrontend = true;

    #[Assert\Image(maxSize: '10M', mimeTypes: [
        AssetsManager::MIME_IMAGE_JPG_TYPE,
        AssetsManager::MIME_IMAGE_JPEG_TYPE,
        AssetsManager::MIME_IMAGE_PNG_TYPE,
        AssetsManager::MIME_IMAGE_GIF_TYPE,
    ], minWidth: 1200)]
    #[Vich\UploadableField(mapping: 'projects', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->projectCategories = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getTranslations(): ?Collection
    {
        return $this->translations;
    }

    public function setTranslations(?Collection $translations): self
    {
        $this->translations = $translations;

        return $this;
    }

    public function addTranslation(ProjectTranslation $translation): self
    {
        if (!$this->translations->contains($translation) && $translation->getContent()) {
            $this->translations->add($translation);
            $translation->setObject($this);
        }

        return $this;
    }

    public function removeTranslation(ProjectTranslation $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }

        return $this;
    }

    public function getProjectCategories(): ?Collection
    {
        return $this->projectCategories;
    }

    public function setProjectCategories(?Collection $projectCategories): self
    {
        $this->projectCategories = $projectCategories;

        return $this;
    }

    public function addProjectCategory(ProjectCategory $category): self
    {
        if (!$this->projectCategories->contains($category)) {
            $this->projectCategories->add($category);
        }

        return $this;
    }

    public function removeProjectCategory(ProjectCategory $category): self
    {
        if ($this->projectCategories->contains($category)) {
            $this->projectCategories->removeElement($category);
        }

        return $this;
    }

    public function getImages(): ?Collection
    {
        return $this->images;
    }

    public function setImages(?Collection $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function addImage(ProjectImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProject($this);
        }

        return $this;
    }

    public function removeImage(ProjectImage $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }

        return $this;
    }

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
