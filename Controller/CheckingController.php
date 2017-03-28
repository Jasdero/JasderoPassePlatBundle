<?php

namespace Jasdero\PassePlatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

abstract class CheckingController extends Controller
{


    /**
     * Used to check that a user exists
     *
     * @param string $email A user mail
     * @return Response
     */
    protected function validateUser($email)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JasderoPassePlatBundle:User')->findOneBy(['email' => $email]);
        if (!$user) {
            $response = false;
        } else {
            $response = $user;
        }

        return ($response);
    }


    /**
     * Used function to check that an order is valid : must have at least one product
     * @param array $products
     * @return bool
     */
    protected function validateOrder(array $products)
    {
        $em = $this->getDoctrine()->getManager();
        $response = true;

        foreach ($products as $product) {
            if(null === $em->getRepository('JasderoPassePlatBundle:Catalog')->findOneBy(['id'=>$product]) ){
                $response = false;
            }
        }
        return ($response);

    }
}
