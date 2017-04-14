<?php

namespace Jasdero\PassePlatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Jasdero\PassePlatBundle\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var Category[]
     * @ORM\OneToMany(targetEntity="Catalog", mappedBy="branch")
     */
    private $catalogs;

    /**
     * @var Category[]
     * @ORM\OneToMany(targetEntity="Catalog", mappedBy="category")
     */
    private $catalogs2;

    /**
     * @var Category[]
     * @ORM\OneToMany(targetEntity="Catalog", mappedBy="subCategory")
     */
    private $catalogs3;


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
     * Set name
     *
     * @param string $name
     *
     * @return Category
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
     * Constructor
     */
    public function __construct()
    {
        $this->catalogs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->catalogs2 = new \Doctrine\Common\Collections\ArrayCollection();
        $this->catalogs3 = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add catalog
     *
     * @param \Jasdero\PassePlatBundle\Entity\Catalog $catalog
     *
     * @return Category
     */
    public function addCatalog(\Jasdero\PassePlatBundle\Entity\Catalog $catalog)
    {
        $this->catalogs[] = $catalog;

        return $this;
    }

    /**
     * Remove catalog
     *
     * @param \Jasdero\PassePlatBundle\Entity\Catalog $catalog
     */
    public function removeCatalog(\Jasdero\PassePlatBundle\Entity\Catalog $catalog)
    {
        $this->catalogs->removeElement($catalog);
    }

    /**
     * Get catalogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCatalogs()
    {
        return $this->catalogs;
    }

    /**
     * Add catalogs2
     *
     * @param \Jasdero\PassePlatBundle\Entity\Catalog $catalogs2
     *
     * @return Category
     */
    public function addCatalogs2(\Jasdero\PassePlatBundle\Entity\Catalog $catalogs2)
    {
        $this->catalogs2[] = $catalogs2;

        return $this;
    }

    /**
     * Remove catalogs2
     *
     * @param \Jasdero\PassePlatBundle\Entity\Catalog $catalogs2
     */
    public function removeCatalogs2(\Jasdero\PassePlatBundle\Entity\Catalog $catalogs2)
    {
        $this->catalogs2->removeElement($catalogs2);
    }

    /**
     * Get catalogs2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCatalogs2()
    {
        return $this->catalogs2;
    }

    /**
     * Add catalogs3
     *
     * @param \Jasdero\PassePlatBundle\Entity\Catalog $catalogs3
     *
     * @return Category
     */
    public function addCatalogs3(\Jasdero\PassePlatBundle\Entity\Catalog $catalogs3)
    {
        $this->catalogs3[] = $catalogs3;

        return $this;
    }

    /**
     * Remove catalogs3
     *
     * @param \Jasdero\PassePlatBundle\Entity\Catalog $catalogs3
     */
    public function removeCatalogs3(\Jasdero\PassePlatBundle\Entity\Catalog $catalogs3)
    {
        $this->catalogs3->removeElement($catalogs3);
    }

    /**
     * Get catalogs3
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCatalogs3()
    {
        return $this->catalogs3;
    }
}
