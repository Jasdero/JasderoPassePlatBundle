<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\Vat;
use Jasdero\PassePlatBundle\Form\Type\VatType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Vat controller.
 *
 * @Route("vat")
 */
class VatController extends Controller
{
    /**
     * Lists all vat entities.
     *
     * @Route("/", name="vat_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $vats = $em->getRepository('JasderoPassePlatBundle:Vat')->findAll();

        return $this->render('@JasderoPassePlat/vat/index.html.twig', array(
            'vats' => $vats,
        ));
    }

    /**
     * Creates a new vat entity.
     *
     * @Route("/new", name="vat_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $vat = new Vat();
        $form = $this->createForm(VatType::class, $vat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vat);
            $em->flush();

            return $this->redirectToRoute('vat_show', array('id' => $vat->getId()));
        }

        return $this->render('@JasderoPassePlat/vat/new.html.twig', array(
            'vat' => $vat,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a vat entity.
     *
     * @Route("/{id}", name="vat_show")
     * @Method("GET")
     * @param Vat $vat
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Vat $vat)
    {
        $deleteForm = $this->createDeleteForm($vat);

        return $this->render('@JasderoPassePlat/vat/show.html.twig', array(
            'vat' => $vat,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing vat entity.
     *
     * @Route("/{id}/edit", name="vat_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Vat $vat
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Vat $vat)
    {
        $deleteForm = $this->createDeleteForm($vat);
        $editForm = $this->createForm(VatType::class, $vat);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vat_show', array('id' => $vat->getId()));
        }

        return $this->render('@JasderoPassePlat/vat/edit.html.twig', array(
            'vat' => $vat,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a vat entity.
     *
     * @Route("/{id}", name="vat_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Vat $vat
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Vat $vat)
    {
        $form = $this->createDeleteForm($vat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($vat);
            $em->flush();
        }

        return $this->redirectToRoute('vat_index');
    }

    /**
     * Creates a form to delete a vat entity.
     *
     * @param Vat $vat The vat entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Vat $vat)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vat_delete', array('id' => $vat->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
