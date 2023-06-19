<?php

namespace App\Entity;

use App\Entity\Traits\NameTrait;
use App\Repository\ProjectCategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectCategoryRepository::class)]
class ProjectCategory extends AbstractBase
{
    use NameTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    public function __toString(): string
    {
        return $this->id ? $this->getName() : self::DEFAULT_NULL_STRING;
    }
}
