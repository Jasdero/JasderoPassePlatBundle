<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\Catalog;
use Jasdero\PassePlatBundle\Form\Type\CatalogType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Catalog controller.
 *
 * @Route("catalog")
 */
class CatalogController extends Controller
{

    /**
     * function to display how many orders include given catalog
     * @param Catalog $catalog
     * @return Response
     */
    public function catalogInOrdersAction(Catalog $catalog)
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('JasderoPassePlatBundle:Product')->findOrderByCatalog($catalog);

        return New Response(count($orders));
    }

    /**
     * Lists all catalog entities with stats
     * @Route("/", name="catalog_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $catalogs = $em->getRepository('JasderoPassePlatBundle:Catalog')->findAllCatalogsWithAssociations();
        $totalOrders = $em->getRepository('JasderoPassePlatBundle:Orders')->countOrders();
        $totalProducts = $em->getRepository('JasderoPassePlatBundle:Product')->countProducts();

        return $this->render('@JasderoPassePlat/catalog/index.html.twig', array(
            'catalogs' => $catalogs,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
        ));
    }


    /**
     * Creates a new catalog entity.
     *
     * @Route("/new", name="catalog_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $catalog = new Catalog();
        $form = $this->createForm(CatalogType::class, $catalog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($catalog);
            $em->flush();

            return $this->redirectToRoute('catalog_show', array('id' => $catalog->getId()));
        }

        return $this->render('@JasderoPassePlat/catalog/new.html.twig', array(
            'catalog' => $catalog,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a catalog entity.
     *
     * @Route("/{id}", name="catalog_show")
     * @Method("GET")
     * @param Catalog $catalog
     * @return Response
     */
    public function showAction(Catalog $catalog)
    {
        $deleteForm = $this->createDeleteForm($catalog);

        return $this->render('@JasderoPassePlat/catalog/show.html.twig', array(
            'catalog' => $catalog,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing catalog entity.
     *
     * @Route("/{id}/edit", name="catalog_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Catalog $catalog
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Request $request, Catalog $catalog)
    {
        $deleteForm = $this->createDeleteForm($catalog);
        $editForm = $this->createForm(CatalogType::class, $catalog);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('catalog_show', array('id' => $catalog->getId()));
        }

        return $this->render('@JasderoPassePlat/catalog/edit.html.twig', array(
            'catalog' => $catalog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a catalog entity.
     *
     * @Route("/{id}", name="catalog_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Catalog $catalog
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Catalog $catalog)
    {
        $form = $this->createDeleteForm($catalog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($catalog);
            $em->flush();
        }

        return $this->redirectToRoute('catalog_index');
    }

    /**
     * Creates a form to delete a catalog entity.
     *
     * @param Catalog $catalog The catalog entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Catalog $catalog)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('catalog_delete', array('id' => $catalog->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


}
