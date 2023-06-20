<?php

namespace App\Entity;

use App\Entity\Traits\NameTrait;
use App\Repository\PagePathVisitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PagePathVisitRepository::class)]
#[UniqueEntity(['name', 'createdAt'])]
class PagePathVisit extends AbstractBase
{
    use NameTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0])]
    private int $screenPageViews = 0;

    public function getScreenPageViews(): int
    {
        return $this->screenPageViews;
    }

    public function setScreenPageViews(int $screenPageViews): self
    {
        $this->screenPageViews = $screenPageViews;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getName() : self::DEFAULT_NULL_STRING;
    }
}
