<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MainController extends Controller
{

    /**
     * access and display of dashboard
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardAction()
    {
        $driveActivation = $this->get('service_container')->getParameter('drive_activation');
        $em = $this->getDoctrine()->getManager();
        $states = $em->getRepository('JasderoPassePlatBundle:State')->findAllStatesWithAssociations();


        return $this->render('JasderoPassePlatBundle:main:dashboard.html.twig', array(
            'driveActivation' => $driveActivation,
            'states' => $states,
        ));

    }

    /**
     * access and display user info
     * @Route("/user/{id}", name = "user_detail")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function userInfoAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('JasderoPassePlatBundle:Orders')->findBy(['user'=> $user]);
        $nbOrders = count($orders);

        return $this->render('@JasderoPassePlat/main/userDetail.html.twig', array(
            'user' => $user,
            'orders' => $orders,
            'nbOrders' => $nbOrders
        ));
    }

}
