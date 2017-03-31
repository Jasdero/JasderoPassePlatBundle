<?php


namespace Jasdero\PassePlatBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OrdersRepository extends EntityRepository
{
    public function countOrders()
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('count(o.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findOneByIdWithAssociations($id)
    {
        $qb = $this->createQueryBuilder('o');

        $qb
            ->leftJoin('o.products', 'p')
            ->addSelect('p')
            ->leftJoin('p.catalog', 'c')
            ->addSelect('c')
            ->leftJoin('o.user', 'u')
            ->addSelect('u')
            ->leftJoin('o.state', 's')
            ->addSelect('s')
            ->where('o.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getSingleResult();
    }

    public function findByStateWithAssociations($state)
    {
        $qb = $this->createQueryBuilder('o');

        $qb
            ->leftJoin('o.products', 'p')
            ->addSelect('p')
            ->leftJoin('p.catalog', 'c')
            ->addSelect('c')
            ->leftJoin('o.user', 'u')
            ->addSelect('u')
            ->leftJoin('o.state', 's')
            ->addSelect('s')
            ->where('o.state = :state')
            ->setParameter('state', $state);

        return $qb->getQuery()->getResult();
    }

    public function findMaxWeightInOrder($order)
    {
        $qb = $this->createQueryBuilder('o');

        $qb
            ->select('max(s.weight)')
            ->leftJoin('o.products', 'p')
            ->leftJoin('p.state', 's')
            ->where('o.id = :id')
            ->setParameter('id', $order);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByMultipleStates(array $states)
    {
        $qb = $this->createQueryBuilder('o');
        $qb
            ->leftJoin('o.state', 's')
            ->where('s.id IN (:states)')
            ->setParameter('states', $states);


        return $qb->getQuery()->getResult();
    }
}
