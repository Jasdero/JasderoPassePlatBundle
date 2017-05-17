<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\Comment;
use Jasdero\PassePlatBundle\Entity\Orders;
use Jasdero\PassePlatBundle\Entity\Product;
use Jasdero\PassePlatBundle\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


/**
 * Comment controller.
 *
 * @Route("comment")
 */
class CommentController extends Controller
{


    /**
     * Deletes a comment entity from order
     *
     * @Route("/order/{id}", name="comment_delete_from_order")
     * @Method("DELETE")
     * @param Comment $comment
     * @return Response
     */
    public function deleteFromOrderAction(Comment $comment)
    {

            $em = $this->getDoctrine()->getManager();
            $order = $comment->getOrder();
            $order->removeComment($comment);
            $em->remove($comment);
            $em->flush();

        return new Response("The comment was deleted");
    }

    /**
     * Deletes a comment entity from product
     *
     * @Route("/product/{id}", name="comment_delete_from_product")
     * @Method("DELETE")
     * @param Comment $comment
     * @return Response
     */
    public function deleteFromProductAction(Comment $comment)
    {

        $em = $this->getDoctrine()->getManager();
        $product = $comment->getProduct();
        $product->removeComment($comment);
        $em->remove($comment);
        $em->flush();

        return new Response("The comment was deleted");
    }

    /**
     * Edit a comment entity
     * @Route("/{id}/edit", name="comment_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Comment $comment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function editAction(Request $request, Comment $comment)
    {
        $editForm = $this->createForm(CommentType::class, $comment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            if($comment->getProduct()){
                return $this->redirectToRoute('product_show', array('id' => $comment->getProduct()->getId()));
            } else {
                return $this->redirectToRoute('orders_show', array('id' => $comment->getOrder()->getId()));
            }
        }

        return $this->render('@JasderoPassePlat/comment/edit.html.twig', array(
            'vat' => $comment,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * new comment on order
     * @Route("/order/{id}/new", name="new_comment_on_order")
     * @param Request $request
     * @param Orders $order
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function newCommentOnOrder(Request $request, Orders $order)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setOrder($order);
            if(!$this->getUser()){
                $comment->setAuthor('Anonymous');
            } else {
                $comment->setAuthor($this->getUser()->getUsername());
            }
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('orders_show', array('id' => $order->getId()));
        }

        return $this->render('@JasderoPassePlat/comment/new.html.twig', array(
            'comment' => $comment,
            'order' => $order,
            'form' => $form->createView(),
        ));
    }

    /**
     * new comment on order
     * @Route("/product/{id}/new", name="new_comment_on_product")
     * @param Request $request
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function newCommentOnProduct(Request $request, Product $product)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setProduct($product);
            if(!$this->getUser()){
                $comment->setAuthor('Anonymous');
            } else {
                $comment->setAuthor($this->getUser()->getUsername());
            }
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('product_show', array('id' => $product->getId()));
        }

        return $this->render('@JasderoPassePlat/comment/new.html.twig', array(
            'comment' => $comment,
            'order' => null,
            'product' => $product,
            'form' => $form->createView(),
        ));
    }
}
