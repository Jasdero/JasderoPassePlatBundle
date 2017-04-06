<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\State;
use Jasdero\PassePlatBundle\Form\Type\StateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * State controller.
 *
 * @Route("admin/state")
 */
class StateController extends Controller
{


    //token management
    /**
     * @return mixed
     */
    protected function csrfProtect()
    {
        // store token into session
        $session = $this->get('session');          // expired token treatment
        $expire = $session->get('bjp_token_expire');
        if (!empty($expire) && ($expire < time())) {
            $session->remove('bjp_token');
            $session->remove('bjp_token_expire');
        }          // token generation
        if (empty($session->get('bjp_token'))) {
            $session->set('bjp_token', bin2hex(random_bytes(10)));
            $session->set('bjp_token_expire', time() + 3600);
        }
        return $session->get('bjp_token');
    }

    //override render method to send token
    /**
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        // generate token
        $parameters['bjptoken'] = $this->csrfProtect();
        // call to parent render()
        return parent::render($view, $parameters, $response);
    }

    //also sending token to compare when doing Ajax
    /**
     * Lists all state entities.
     *
     * @Route("/", name="state_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $states = $em->getRepository('JasderoPassePlatBundle:State')->findAllStatesWithAssociations();
        $totalProducts = $em->getRepository('JasderoPassePlatBundle:Product')->countProducts();
        $totalOrders = $em->getRepository('JasderoPassePlatBundle:Orders')->countOrders();
        $driveActivation = $this->get('service_container')->getParameter('drive_activation');


        return $this->render('@JasderoPassePlat/state/index.html.twig', array(
            'states' => $states,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'driveActivation' => $driveActivation,
            'token' => $this->get('session')->get('bjp_token'),
        ));
    }

    /**
     * Creates a new state entity.
     *
     * @Route("/new", name="state_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $state = new State();
        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($state);
            $em->flush();

            return $this->redirectToRoute('state_show', array('id' => $state->getId()));
        }

        return $this->render('@JasderoPassePlat/state/new.html.twig', array(
            'state' => $state,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a state entity.
     *
     * @Route("/{id}", name="state_show")
     * @Method("GET")
     * @param State $state
     * @return Response
     */
    public function showAction(State $state)
    {
        $deleteForm = $this->createDeleteForm($state);

        return $this->render('@JasderoPassePlat/state/show.html.twig', array(
            'state' => $state,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing state entity.
     *
     * @Route("/{id}/edit", name="state_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param State $state
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Request $request, State $state)
    {
        $deleteForm = $this->createDeleteForm($state);
        $editForm = $this->createForm(StateType::class, $state);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('state_show', array('id' => $state->getId()));
        }

        return $this->render('@JasderoPassePlat/state/edit.html.twig', array(
            'state' => $state,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a state entity.
     *
     * @Route("/{id}", name="state_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param State $state
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, State $state)
    {
        $form = $this->createDeleteForm($state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($state);
            $em->flush();
        }

        return $this->redirectToRoute('state_index');
    }

    //method to change statuses weights from Ajax call
    /**
     *
     * @Route("/dynamicChange", name="weight_change")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function weightChangeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $responseText = '';

        //checking Ajax and bjp-token
        if ((!$request->isXmlHttpRequest()) OR($request->request->get('token') !== $request->getSession()->get('bjp_token'))) {
            $responseText = 'ERROR';
        }

        //retrieving new table order
        $newOrder = $request->request->get('request');

        //checking array type
        if (!is_array($newOrder)) {
            $responseText = 'ERROR';
        }

        //checking array values
        foreach ($newOrder as $value) {
            if (preg_match('#\D#', $value)) {
                $responseText = 'ERROR';
            }
        }

        //redirect to index if something wrong
        if ($responseText == 'ERROR') {
            return $this->redirectToRoute('state_index');
        } else {
            //doing the changes if everything is OK

            //cleaning array
            array_shift($newOrder);

            //setting statuses weights
            //array to catch modified states
            $modifiedStates = [];
            foreach ($newOrder as $key => $stateId) {
                $state = $em->getRepository('JasderoPassePlatBundle:State')->findOneBy(['id' => $stateId]);
                $newWeight = 1000 - ($key * 100);
                $currentWeight = $state->getWeight();
                    //comparison to work only on modified states
                    if ($newWeight !== $currentWeight){
                        $modifiedStates[] = $state->getId();
                        $state->setWeight($newWeight);
                        $em->persist($state);
                    }
            }
            $em->flush();

            //updating concerned orders statuses
            $orders = $em->getRepository('JasderoPassePlatBundle:Orders')->findByMultipleStates($modifiedStates);
            $this->get('jasdero_passe_plat.order_status')->multipleOrdersStatus($orders);

            return new Response('Everything is ok');
        }
    }



    /**
     * Creates a form to delete a state entity.
     * @param State $state The state entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(State $state)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('state_delete', array('id' => $state->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
