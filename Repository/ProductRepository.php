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

    public function countProducts()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('count(p.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

}
