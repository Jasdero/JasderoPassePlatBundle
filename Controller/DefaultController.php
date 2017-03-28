<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {

        return $this->render('JasderoPassePlatBundle:Default:index.html.twig');
    }

    /**
     * access and display of dashboard
     * @Route("/admin/dashboard", name="dashboard")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $states = $em->getRepository('JasderoPassePlatBundle:State')->findAllStatesWithAssociations();


        return $this->render('JasderoPassePlatBundle:Admin:dashboard.html.twig', array(
            'states' => $states,
        ));

    }

    /**
     * access and display user info
     * @Route("/admin/user/{id}", name = "user_detail")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function userInfoAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('JasderoPassePlatBundle:Orders')->findBy(['user'=> $user]);
        $nbOrders = count($orders);

        return $this->render('@JasderoPassePlat/Admin/userDetail.html.twig', array(
            'user' => $user,
            'orders' => $orders,
            'nbOrders' => $nbOrders
        ));
    }


}
