<?php

namespace App\Repository;

use App\Entity\ExpenseCategory;
use App\Enum\SortOrderEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExpenseCategory>
 *
 * @method ExpenseCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpenseCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpenseCategory[]    findAll()
 * @method ExpenseCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseCategory::class);
    }

    public function update(bool $flush = false): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(ExpenseCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        $this->update($flush);
    }

    public function remove(ExpenseCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        $this->update($flush);
    }

    public function getAllSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('ec')->orderBy('ec.name', SortOrderEnum::ASCENDING->value);
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
