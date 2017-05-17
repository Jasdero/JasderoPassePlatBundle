<?php

namespace Jasdero\PassePlatBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="Jasdero\PassePlatBundle\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="pretax_price", type="float", precision=10, scale=0, nullable=false)
     */
    private $pretaxPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="vat_rate", type="float", precision=10, scale=0, nullable=true)
     */
    private $vatRate;

    /**
     * @var State
     *
     * @ORM\ManyToOne(targetEntity="State", inversedBy="products")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $state;


    /**
     * @var Catalog
     *
     * @ORM\ManyToOne(targetEntity="Catalog", inversedBy="products")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="catalog_id", referencedColumnName="id")
     * })
     */
    private $catalog;

    /**
     * @var Orders
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="products")
     * @ORM\JoinColumn(name="orders_id", referencedColumnName="id")
     */
    private $orders;

    /**
     * @var Comment
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="product", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=true)
     * @Assert\Valid
     */
    private $comments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pretaxPrice
     *
     * @param float $pretaxPrice
     *
     * @return Product
     */
    public function setPretaxPrice($pretaxPrice)
    {
        $this->pretaxPrice = $pretaxPrice;

        return $this;
    }

    /**
     * Get pretaxPrice
     *
     * @return float
     */
    public function getPretaxPrice()
    {
        return $this->pretaxPrice;
    }

    /**
     * Set vatRate
     *
     * @param float $vatRate
     *
     * @return Product
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    /**
     * Get vatRate
     *
     * @return float
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * Set state
     *
     * @param \Jasdero\PassePlatBundle\Entity\State $state
     *
     * @return Product
     */
    public function setState(\Jasdero\PassePlatBundle\Entity\State $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return \Jasdero\PassePlatBundle\Entity\State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set catalog
     *
     * @param \Jasdero\PassePlatBundle\Entity\Catalog $catalog
     *
     * @return Product
     */
    public function setCatalog(\Jasdero\PassePlatBundle\Entity\Catalog $catalog = null)
    {
        $this->catalog = $catalog;

        return $this;
    }

    /**
     * Get catalog
     *
     * @return \Jasdero\PassePlatBundle\Entity\Catalog
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * Set orders
     *
     * @param \Jasdero\PassePlatBundle\Entity\Orders $orders
     *
     * @return Product
     */
    public function setOrders(\Jasdero\PassePlatBundle\Entity\Orders $orders = null)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return \Jasdero\PassePlatBundle\Entity\Orders
     */
    public function getOrders()
    {
        return $this->orders;
    }


    /**
     * Add comment
     *
     * @param \Jasdero\PassePlatBundle\Entity\Comment $comment
     *
     * @return Product
     */
    public function addComment(\Jasdero\PassePlatBundle\Entity\Comment $comment)
    {
        $comment->setProduct($this);
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \Jasdero\PassePlatBundle\Entity\Comment $comment
     */
    public function removeComment(\Jasdero\PassePlatBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }


}
