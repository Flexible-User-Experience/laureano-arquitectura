<?php

namespace App\Repository;

use App\Entity\InvoiceLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvoiceLine>
 *
 * @method InvoiceLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceLine[]    findAll()
 * @method InvoiceLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceLine::class);
    }

    public function add(InvoiceLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InvoiceLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
