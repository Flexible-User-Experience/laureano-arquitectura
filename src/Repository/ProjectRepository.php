<?php

namespace App\Repository;

use App\Entity\Project;
use App\Enum\SortOrderEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function update(bool $flush = false): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        $this->update($flush);
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        $this->update($flush);
    }

    public function getActiveSortedByPositionQB(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.active = :active')
            ->setParameter('active', true)
            ->orderBy('p.position', SortOrderEnum::ASCENDING->value)
        ;
    }

    public function getActiveSortedByPositionQ(): Query
    {
        return $this->getActiveSortedByPositionQB()->getQuery();
    }

    public function getActiveSortedByPosition(): array
    {
        return $this->getActiveSortedByPositionQ()->getResult();
    }

    public function getActiveAndShowInFrontendSortedByPositionQB(): QueryBuilder
    {
        return $this->getActiveSortedByPositionQB()
            ->andWhere('p.showInFrontend = :show')
            ->setParameter('show', true)
        ;
    }

    public function getActiveAndShowInFrontendSortedByPositionQ(): Query
    {
        return $this->getActiveAndShowInFrontendSortedByPositionQB()->getQuery();
    }

    public function getActiveAndShowInFrontendSortedByPosition(): array
    {
        return $this->getActiveAndShowInFrontendSortedByPositionQ()->getResult();
    }

    public function getAllSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('p')->orderBy('p.name', SortOrderEnum::ASCENDING->value);
    }

    public function getAllSortedByNameQ(): Query
    {
        return $this->getAllSortedByNameQB()->getQuery();
    }

    public function getAllSortedByName(): array
    {
        return $this->getAllSortedByNameQ()->getResult();
    }
}
