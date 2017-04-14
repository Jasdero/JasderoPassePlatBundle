<?php

namespace Jasdero\PassePlatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Jasdero\PassePlatBundle\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var Orders
     * @ORM\OneToOne(targetEntity="Orders", mappedBy="comment")
     */
    private $order;

    /**
     * @var Product
     * @ORM\OneToOne(targetEntity="Product", mappedBy="comment")
     */
    private $product;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }



    /**
     * Set order
     *
     * @param \Jasdero\PassePlatBundle\Entity\Orders $order
     *
     * @return Comment
     */
    public function setOrder(\Jasdero\PassePlatBundle\Entity\Orders $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \Jasdero\PassePlatBundle\Entity\Orders
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set product
     *
     * @param \Jasdero\PassePlatBundle\Entity\Product $product
     *
     * @return Comment
     */
    public function setProduct(\Jasdero\PassePlatBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Jasdero\PassePlatBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
