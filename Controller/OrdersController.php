<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\Catalog;
use Jasdero\PassePlatBundle\Entity\Orders;
use Jasdero\PassePlatBundle\Entity\Product;
use Jasdero\PassePlatBundle\Entity\State;
use Jasdero\PassePlatBundle\Entity\User;
use Jasdero\PassePlatBundle\Form\Type\OrdersType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Order controller.
 *
 */
class OrdersController extends Controller
{


    /**
     * Lists all order entities. Uses pagination
     *
     * @Route("/admin/orders/", name="orders_index")
     * @Method("GET")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $driveActivation = $this->get('service_container')->getParameter('drive_activation');


        $queryBuilder = $em->getRepository('JasderoPassePlatBundle:Orders')->createQueryBuilder('o');
        $queryBuilder
            ->leftJoin('o.products', 'p')
            ->addSelect('p')
            ->leftJoin('p.catalog', 'c')
            ->addSelect('c')
            ->leftJoin('o.user', 'u')
            ->addSelect('u')
            ->leftJoin('o.state', 's')
            ->addSelect('s');
        $query = $queryBuilder->getQuery();

        $orders = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 10)/*limit per page*/
        );


        return $this->render('@JasderoPassePlat/orders/index.html.twig', array(
            'orders' => $orders,
            'driveActivation' => $driveActivation,
        ));
    }

    /**
     * Creates a new order entity. 2 possible ways : through scanning the drive folder or through the site
     *
     * @Method({"GET", "POST"})
     * @param User $user an authenticated user
     * @param array $products an array of ordered products
     * @param string|null $comments
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function newAction(User $user, array $products, string $comments = null)
    {
        $order = new Orders();

        //getting basic state to set products : part to improve
        $em = $this->getDoctrine()->getManager();
        $state = $em->getRepository('JasderoPassePlatBundle:State')->findOneBy(['id' => 1]);


        //setting orders data
        $order->setUser($user);
        if($comments){
            $order->setComments($comments);
        }
        $em->persist($order);
        $em->flush();

        //creating each product line

        foreach ($products as $product) {
            $catalog = $em->getRepository('JasderoPassePlatBundle:Catalog')->findOneBy(['id' => $product]);

            $newProductLine = new Product();
            $newProductLine->setState($state);
            $newProductLine->setOrders($order);
            $newProductLine->setCatalog($catalog);
            $newProductLine->setPretaxPrice($catalog->getPretaxPrice());
            if($catalog->getVat() !== null){
                $newProductLine->setVatRate($catalog->getVat()->getRate());
            }
            $em->persist($newProductLine);
        }
        $em->flush();


        //setting order status
        $this->get('jasdero_passe_plat.order_status')->orderStatusAction($order);

        //give back the new order id
        return New Response ($order->getId());

    }

    /**
     * Finds and displays an order entity with its associated products
     *
     * @Route("/admin/orders/{id}", name="orders_show")
     * @Method("GET")
     * @param Orders $order
     * @return Response
     */
    public function showAction(Orders $order)
    {
        $deleteForm = $this->createDeleteForm($order);

        //getting products contained inside the order

        return $this->render('@JasderoPassePlat/orders/show.html.twig', array(
            'order' => $order,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing order entity.
     *
     * @Route("/admin/orders/{id}/edit", name="orders_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Orders $order
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Request $request, Orders $order)
    {
        $deleteForm = $this->createDeleteForm($order);
        $editForm = $this->createForm(OrdersType::class, $order);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('orders_show', array('id' => $order->getId()));
        }

        return $this->render('@JasderoPassePlat/orders/edit.html.twig', array(
            'order' => $order,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes an order entity.
     *
     * @Route("/admin/orders/{id}", name="orders_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Orders $order
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Orders $order)
    {
        $form = $this->createDeleteForm($order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($order);
            $em->flush();
        }

        return $this->redirectToRoute('orders_index');
    }

    /**
     * Creates a form to delete a order entity.
     *
     * @param Orders $order The order entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Orders $order)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orders_delete', array('id' => $order->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * orders sorted by status from the status page
     * @Route("/admin/orders/status/{id}", name="orders_by_status")
     * @param State $state
     * @return Response
     */
    public function ordersByStatusAction(State $state)
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('JasderoPassePlatBundle:Orders')->findByStateWithAssociations($state->getId());
        $driveActivation = $this->get('service_container')->getParameter('drive_activation');

        return $this->render('@JasderoPassePlat/orders/ordersFiltered.html.twig', array(
            'orders' => $orders,
            'driveActivation' => $driveActivation,
        ));
    }

    /**
     * orders filtered by catalog
     * @Route("/admin/orders/catalog/{id}", name="orders_by_catalog")
     * @param Catalog $catalog
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ordersByCatalogAction(Catalog $catalog)
    {
        $em = $this->getDoctrine()->getManager();
        $driveActivation = $this->get('service_container')->getParameter('drive_activation');

        //getting Orders Id
        $ordersId = $em->getRepository('JasderoPassePlatBundle:Product')->findOrderByCatalog($catalog);
        //getting Orders
        $orders = [];
        foreach ($ordersId as $order) {
            $orders[] = $em->getRepository('JasderoPassePlatBundle:Orders')->findOneByIdWithAssociations($order['id']);
        }

        return $this->render('@JasderoPassePlat/orders/ordersFiltered.html.twig', array(
            'orders' => $orders,
            'driveActivation' => $driveActivation,
        ));
    }

    /**
     * orders filtered by user
     * @Route("/admin/orders/user/{id}",name = "orders_by_user")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function ordersByUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('JasderoPassePlatBundle:Orders')->findBy(['user'=>$user]);
        $driveActivation = $this->get('service_container')->getParameter('drive_activation');

        return $this->render('@JasderoPassePlat/orders/ordersFiltered.html.twig', array(
            'orders' => $orders,
            'driveActivation' => $driveActivation,
        ));

    }
}
