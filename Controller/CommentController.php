<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

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

        return new Response();
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

        return new Response();
    }


}
