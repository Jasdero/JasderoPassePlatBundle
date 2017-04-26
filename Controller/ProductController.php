<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\Catalog;
use Jasdero\PassePlatBundle\Entity\Product;
use Jasdero\PassePlatBundle\Entity\State;
use Jasdero\PassePlatBundle\Form\Type\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Product controller.
 *
 * @Route("product")
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities, uses pagination
     *
     * @Route("/", name="product_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $queryBuilder = $em->getRepository('JasderoPassePlatBundle:Product')->createQueryBuilder('p');
        $queryBuilder
            ->leftJoin('p.catalog', 'c')
            ->addSelect('c')
            ->leftJoin('p.orders', 'o')
            ->addSelect('o')
            ->leftJoin('p.state', 's')
            ->addSelect('s')
            ->leftJoin('c.category', 'k')
            ->addSelect('k');
        $query = $queryBuilder->getQuery();

        $products = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 10)/*limit per page*/
        );

        return $this->render('@JasderoPassePlat/product/index.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * Creates a new product entity.
     *
     * @Route("/new", name="product_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', array('id' => $product->getId()));
        }

        return $this->render('@JasderoPassePlat/product/new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("/{id}", name="product_show")
     * @Method("GET")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('@JasderoPassePlat/product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm(ProductType::class, $product);
        $editForm->handleRequest($request);
        $driveActivation = $this->get('service_container')->getParameter('drive_activation');


        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //updating order status
            $this->get('jasdero_passe_plat.order_status')->orderStatusAction($product->getOrders());
            if ($driveActivation) {
                $this->get('jasdero_passe_plat.drive_folder_as_status')->driveFolder($product->getState()->getName(), $product->getOrders()->getId());
            }


            return $this->redirectToRoute('product_show', array('id' => $product->getId()));
        }

        return $this->render('@JasderoPassePlat/product/edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     * @Route("/{id}", name="product_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a product entity.
     * @param Product $product The product entity
     * @return \Symfony\Component\Form\Form The form
     */

    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }


    /**
     * products sorted by statuses
     * @Route("/status/{id}", name="products_by_status")
     * @param State $state
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function productsByStatusAction(State $state)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('JasderoPassePlatBundle:Product')->findBy(['state' => $state->getId()]);

        return $this->render('@JasderoPassePlat/product/productsFiltered.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * products sorted by catalog
     * @Route("/catalog/{id}", name="products_by_catalog")
     * @param Catalog $catalog
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function productsByCatalogAction(Catalog $catalog)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('JasderoPassePlatBundle:Product')->findBy(['catalog' => $catalog->getId()]);

        return $this->render('@JasderoPassePlat/product/productsFiltered.html.twig', array(
            'products' => $products,
        ));
    }

}
