<?php

namespace App\Repository;

use App\Entity\AbstractBase;
use App\Entity\Customer;
use App\Enum\SortOrderEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function update(bool $flush = false): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        $this->update($flush);
    }

    public function remove(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        $this->update($flush);
    }

    public function getAllSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.fiscalName', SortOrderEnum::ASCENDING->value);
    }

    public function getAllSortedByNameQ(): Query
    {
        return $this->getAllSortedByNameQB()->getQuery();
    }

    public function getAllSortedByName(): array
    {
        return $this->getAllSortedByNameQ()->getResult();
    }

    public function getYearlyNewCustomersAmountForYear(int $year): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.fiscalName as amount')
            ->join('c.invoices', 'i')
            ->where('YEAR(i.date) = :year')
            ->setParameter('year', $year)
            ->groupBy('c.fiscalName')
            ->getQuery()
        ;

        return count($query->getArrayResult());
    }

    public function getCumulativeNewCustomersAmountUpToYear(int $year): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.fiscalName as amount')
            ->join('c.invoices', 'i')
            ->where('YEAR(i.date) <= :year')
            ->setParameter('year', $year)
            ->groupBy('c.fiscalName')
            ->getQuery()
        ;

        return count($query->getArrayResult());
    }

    public function getTopTenCustomerEarningsQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('c.fiscalName')
            ->addSelect('c.city')
            ->addSelect('SUM(i.taxBaseAmount) AS invoiced')
            ->join('c.invoices', 'i')
            ->groupBy('c.fiscalName')
            ->orderBy('invoiced', SortOrderEnum::DESCENDING->value)
            ->setMaxResults(10)
        ;
    }

    public function getTopTenCustomerEarningsQ(): Query
    {
        return $this->getTopTenCustomerEarningsQB()->getQuery();
    }

    public function getTopTenCustomerEarnings(): array
    {
        return $this->getTopTenCustomerEarningsQ()->getScalarResult();
    }

    public function getLast12MonthsTopTenCustomerEarnings(): array
    {
        $date = (new \DateTime())->modify('-12 month');
        $query = $this->getTopTenCustomerEarningsQB()
            ->where('i.date >= :date')
            ->setParameter('date', $date->format(AbstractBase::DATABASE_DATE_STRING_FORMAT))
        ;

        return $query->getQuery()->getScalarResult();
    }

    public function getTotalCustomersAmount(): int
    {
        return count($this->getAllSortedByName());
    }
}
