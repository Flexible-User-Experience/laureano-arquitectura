<?php

namespace App\Repository\Translation;

use App\Entity\Translations\ProjectTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectTranslation>
 *
 * @method ProjectTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectTranslation[]    findAll()
 * @method ProjectTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectTranslation::class);
    }

    public function update(bool $flush = false): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(ProjectTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        $this->update($flush);
    }

    public function remove(ProjectTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        $this->update($flush);
    }
}
