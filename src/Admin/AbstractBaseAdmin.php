<?php

namespace App\Admin;

use App\Enum\SortOrderEnum;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

class AbstractBaseAdmin extends AbstractAdmin
{
    protected EntityManagerInterface $em;

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::PAGE] = 1;
        $sortValues[DatagridInterface::SORT_ORDER] = SortOrderEnum::DESCENDING->value;
        $sortValues[DatagridInterface::SORT_BY] = 'createdAt';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->remove('delete')
            ->remove('batch')
        ;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    public function setEntityManager(EntityManagerInterface $em): self
    {
        $this->em = $em;

        return $this;
    }

    protected function isFormToCreateNewRecord(): bool
    {
        return !$this->id($this->getSubject());
    }
}
