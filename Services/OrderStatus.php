<?php

namespace Jasdero\PassePlatBundle\Services;

use Jasdero\PassePlatBundle\Entity\Orders;
use Doctrine\ORM\EntityManager;

class OrderStatus
{


    /**
     * OrderStatus constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * determine status of order according to products statuses
     * @param Orders $order
     */
    public function orderStatusAction(Orders $order)
    {
        //Getting max weight of products in order
        $maxWeight = $this->em->getRepository('JasderoPassePlatBundle:Orders')->findMaxWeightInOrder($order);

        //Finding corresponding status
        $status = $this->em->getRepository('JasderoPassePlatBundle:State')->findOneBy(['weight' => $maxWeight]);

        //Setting order status
        $order->setState($status);
        $this->em->persist($order);
        $this->em->flush();

    }

    /**
     * same as above but for a list of orders
     * @param array $orders
     */
    public function multipleOrdersStatus(array $orders)
    {
        foreach ($orders as $order) {
            $maxWeight = $this->em->getRepository('JasderoPassePlatBundle:Orders')->findMaxWeightInOrder($order);

            //Finding corresponding status
            $status = $this->em->getRepository('JasderoPassePlatBundle:State')->findOneBy(['weight' => $maxWeight]);

            //Setting order status
            $order->setState($status);
            $order->setDriveSynchro(false);
            $this->em->persist($order);
        }
        $this->em->flush();

    }
}
