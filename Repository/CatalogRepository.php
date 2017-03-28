<?php

namespace Jasdero\PassePlatBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CatalogRepository extends EntityRepository
{
    public function findAllCatalogsWithAssociations()
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->leftJoin('c.products', 'p')
            ->addSelect('p')
            ->leftJoin('c.vat', 'v')
            ->addSelect('v');

        return $qb->getQuery()->getResult();

    }
}
