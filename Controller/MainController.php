<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{

    /**
     * access and display of dashboard
     * @Route("/admin/dashboard", name="dashboard")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $states = $em->getRepository('JasderoPassePlatBundle:State')->findAllStatesWithAssociations();


        return $this->render('JasderoPassePlatBundle:main:dashboard.html.twig', array(
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

        return $this->render('@JasderoPassePlat/main/userDetail.html.twig', array(
            'user' => $user,
            'orders' => $orders,
            'nbOrders' => $nbOrders
        ));
    }

    /**
     * access to command for sync with drive
     * @Route("admin/drive/action", name="drive_action")
     */
    public function showDriveAction()
    {
        return $this->render('@JasderoPassePlat/main/syncWithDrive.html.twig');
    }

    /**
     * synchronizing with drive
     * @Route("/drive/synchro", name="drive_synchro")
     * @return Response
     */
    public function synchAllWithDriveAction()
    {
        $em = $this->getDoctrine()->getManager();
        $affectedOrders = $em->getRepository('JasderoPassePlatBundle:Orders')->findBy(['driveSynchro' => false]);
        $totalOrders = count($affectedOrders);


        if ($totalOrders > 0){
            $this->get('jasdero_passe_plat.drive_folder_as_status')->driveFolder($affectedOrders[0]->getState()->getName(), $affectedOrders[0]->getId());
            return new Response($totalOrders);
        } else {
            return new Response('done');
        }
    }

}
