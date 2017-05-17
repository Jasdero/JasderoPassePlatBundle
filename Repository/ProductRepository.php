<?php


namespace Jasdero\PassePlatBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    public function findOrderByCatalog($id)
    {
        $qb = $this->createQueryBuilder('p');
        $qb ->select('orders.id')
            ->leftJoin('p.orders', 'orders')
            ->leftJoin('p.catalog', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->groupBy('orders.id');

        return $qb->getQuery()->getResult();
    }

    // counting products not in archives
    public function countActiveProducts()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->select('count(p.id)')
            ->leftJoin('p.orders', 'o')
            ->where('o.archive = false');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByStateWithAssociations($state)
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->leftJoin('p.orders', 'o')
            ->addSelect('o')
            ->where('o.archive = false')
            ->leftJoin('p.catalog', 'c')
            ->addSelect('c')
            ->leftJoin('o.user', 'u')
            ->addSelect('u')
            ->leftJoin('p.state', 's')
            ->addSelect('s')
            ->andWhere('p.state = :state')
            ->setParameter('state', $state);


        return $qb->getQuery()->getResult();
    }

}
