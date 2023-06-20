<?php

namespace App\Repository;

use App\Entity\PagePathVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PagePathVisit>
 *
 * @method PagePathVisit|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagePathVisit|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagePathVisit[]    findAll()
 * @method PagePathVisit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagePathVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PagePathVisit::class);
    }

    public function update(bool $flush = false): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(PagePathVisit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        $this->update($flush);
    }

    public function remove(PagePathVisit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        $this->update($flush);
    }
}
