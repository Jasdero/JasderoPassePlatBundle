<?php


namespace Jasdero\PassePlatBundle\Repository;


use Doctrine\ORM\EntityRepository;

class StateRepository extends EntityRepository
{
    public function findAllStatesWithAssociations()
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->leftJoin('s.orders', 'o')
            ->addSelect('o')
            ->leftJoin('s.products', 'p')
            ->addSelect('p')
            ->orderBy('s.weight', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
