<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Enum\SortOrderEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function update(bool $flush = false): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        $this->update($flush);
    }

    public function remove(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        $this->update($flush);
    }

    public function getAllSortedByDateQB(): QueryBuilder
    {
        return $this->createQueryBuilder('i')
            ->select('i', 'c')
            ->join('i.customer', 'c')
            ->orderBy('i.date', SortOrderEnum::DESCENDING->value)
            ->addOrderBy('i.number', SortOrderEnum::DESCENDING->value)
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getLastInvoiceBySeries(string $series): ?Invoice
    {
        return $this->createQueryBuilder('i')
            ->where('i.series = :series')
            ->setParameter('series', $series)
            ->orderBy('i.date', SortOrderEnum::DESCENDING->value)
            ->addOrderBy('i.number', SortOrderEnum::DESCENDING->value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getPreviousInvoice(Invoice $invoice): ?Invoice
    {
        $number = 1;
        if ($invoice->getNumber() > 1) {
            $number = $invoice->getNumber() - 1;
        }

        return $this->getInvoiceBySeriesAndNumber($invoice->getSeries(), $number);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getNextInvoice(Invoice $invoice): ?Invoice
    {
        return $this->getInvoiceBySeriesAndNumber($invoice->getSeries(), $invoice->getNumber() + 1);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getYearlyIncomingAmountForYear(int $year): int
    {
        $query = $this->createQueryBuilder('i')
            ->select('SUM(i.taxBaseAmount) as amount')
            ->where('YEAR(i.date) = :year')
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
        $query = $this->createQueryBuilder('i')
            ->select('SUM(i.taxBaseAmount) as amount')
            ->where('MONTH(i.date) = :month')
            ->andWhere('YEAR(i.date) = :year')
            ->setParameter('month', (int) $date->format('m'))
            ->setParameter('year', (int) $date->format('Y'))
            ->getQuery()
        ;

        return is_null($query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR)) ? 0 : (float) $query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getInvoiceBySeriesAndNumber(string $series, int $number): ?Invoice
    {
        return $this->createQueryBuilder('i')
            ->where('i.series = :series')
            ->andWhere('i.number = :number')
            ->setParameter('series', $series)
            ->setParameter('number', $number)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalInvoicedAmountByCustomer(Customer $customer): int
    {
        $result = $this->createQueryBuilder('i')
            ->select('SUM(i.taxBaseAmount) as amount')
            ->where('i.customer = :customer')
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getSingleResult()
        ;

        return array_key_exists('amount', $result) ? (int) $result['amount'] : 0;
    }

    public function getUnpaidInvoicesSortedByDate(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.hasBeenPaid = :hasBeenPaid')
            ->setParameter('hasBeenPaid', false)
            ->orderBy('i.date', SortOrderEnum::DESCENDING->value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTotalInvoicesAmount(): int
    {
        return count($this->getAllSortedByDateQB()->getQuery()->getResult());
    }
}
