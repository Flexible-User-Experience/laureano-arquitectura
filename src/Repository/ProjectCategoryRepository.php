<?php

namespace App\Repository;

use App\Entity\ProjectCategory;
use App\Enum\SortOrderEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectCategory>
 *
 * @method ProjectCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectCategory[]    findAll()
 * @method ProjectCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectCategory::class);
    }

    public function update(bool $flush = false): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(ProjectCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        $this->update($flush);
    }

    public function remove(ProjectCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        $this->update($flush);
    }

    public function getAllSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('pc')->orderBy('pc.name', SortOrderEnum::ASCENDING->value);
    }

    public function getAllSortedByNameQ(): Query
    {
        return $this->getAllSortedByNameQB()->getQuery();
    }

    public function getAllSortedByName(): array
    {
        return $this->getAllSortedByNameQ()->getResult();
    }

    public function getActiveSortedByNameQB(): QueryBuilder
    {
        return $this->getAllSortedByNameQB()
            ->where('pc.active = :active')
            ->setParameter('active', true)
        ;
    }

    public function getActiveSortedByNameQ(): Query
    {
        return $this->getActiveSortedByNameQB()->getQuery();
    }

    public function getActiveSortedByName(): array
    {
        return $this->getActiveSortedByNameQ()->getResult();
    }
}
