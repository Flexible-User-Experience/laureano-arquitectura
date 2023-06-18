<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function update(bool $flush = false): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(Expense $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        $this->update($flush);
    }

    public function remove(Expense $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        $this->update($flush);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getYearlyExpensedAmountForYear(int $year): int
    {
        $query = $this->createQueryBuilder('e')
            ->select('SUM(e.taxBaseAmount) as amount')
            ->where('YEAR(e.date) = :year')
            ->setParameter('year', $year)
            ->getQuery()
        ;

        return is_null($query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR)) ? 0 : (float) $query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getMonthlyIncomingAmountForDate(\DateTimeInterface $date): int
    {
        $query = $this->createQueryBuilder('e')
            ->select('SUM(e.taxBaseAmount) as amount')
            ->where('MONTH(e.date) = :month')
            ->andWhere('YEAR(e.date) = :year')
            ->setParameter('month', (int) $date->format('m'))
            ->setParameter('year', (int) $date->format('Y'))
            ->getQuery()
        ;

        return is_null($query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR)) ? 0 : (float) $query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }
}
