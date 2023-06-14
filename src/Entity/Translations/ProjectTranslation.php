<?php

namespace App\Entity\Translations;

use App\Entity\Project;
use App\Repository\Translation\ProjectTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

#[ORM\Entity(repositoryClass: ProjectTranslationRepository::class)]
#[ORM\UniqueConstraint(name: 'lookup_project_unique_idx', columns: ['locale', 'object_id', 'field'])]
class ProjectTranslation extends AbstractPersonalTranslation
{
    #[ORM\JoinColumn(name: 'object_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'translations')]
    protected $object;
}
