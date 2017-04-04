<?php

namespace Jasdero\PassePlatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Catalog
 *
 * @ORM\Table(name="catalog")
 * @ORM\Entity(repositoryClass="Jasdero\PassePlatBundle\Repository\CatalogRepository")
 */
class Catalog
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="pretax_price", type="float", precision=10, scale=0, nullable=false)
     */
    private $pretaxPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activated", type="boolean", nullable=false)
     */
    private $activated;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="catalog")
     */
    private $products;

    /**
     * @var \Vat
     *
     * @ORM\ManyToOne(targetEntity="Vat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vat_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $vat;



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
     * Set name
     *
     * @param string $name
     *
     * @return Catalog
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Catalog
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set pretaxPrice
     *
     * @param float $pretaxPrice
     *
     * @return Catalog
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
     * Set activated
     *
     * @param boolean $activated
     *
     * @return Catalog
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated
     *
     * @return boolean
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set vat
     *
     * @param \Jasdero\PassePlatBundle\Entity\Vat $vat
     *
     * @return Catalog
     */
    public function setVat(\Jasdero\PassePlatBundle\Entity\Vat $vat = null)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return \Jasdero\PassePlatBundle\Entity\Vat
     */
    public function getVat()
    {
        return $this->vat;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add product
     *
     * @param \Jasdero\PassePlatBundle\Entity\Product $product
     *
     * @return Catalog
     */
    public function addProduct(\Jasdero\PassePlatBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \Jasdero\PassePlatBundle\Entity\Product $product
     */
    public function removeProduct(\Jasdero\PassePlatBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
