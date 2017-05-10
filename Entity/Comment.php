<?php

namespace Jasdero\PassePlatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\HasLifecycleCallbacks()
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
     * @Assert\Length(
     *      min = 2,
     *      max = 200,
     *      minMessage = "Your comment must be at least {{ limit }} characters long",
     *      maxMessage = "Your comment cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="content", type="string", length=200)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=50, nullable=true)
     */
    private $author;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    private $lastUpdate;

    /**
     * @var Orders
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="comments")
     */
    private $order;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="comments")
     */
    private $product;


    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->lastUpdate = new \DateTime();
    }

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

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Comment
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return Comment
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @ORM\PreUpdate
     */

    public function updateDate()
    {
        $this->setLastUpdate(new \DateTime());
    }
}
