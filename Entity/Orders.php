<?php

namespace Jasdero\PassePlatBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Jasdero\PassePlatBundle\Repository\OrdersRepository")
 */
class Orders
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
     * @var \DateTime
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    private $lastUpdate;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @var Comment
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="order", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=true)
     * @Assert\Valid
     */
    private $comments;

    /**
     * @var boolean
     *
     * @ORM\Column(name="drive_synchro", type="boolean")
     */
    private $driveSynchro = 0;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="State", inversedBy="orders")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="Source", inversedBy="orders")
     * @ORM\JoinColumn(name="source_id", referencedColumnName="id")
     */
    private $source;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="orders", cascade={"remove"})
     */
    private $products;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->products = new ArrayCollection();
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
     * @ORM\PreUpdate
     */

    public function updateDate()
    {
        $this->setLastUpdate(new \DateTime());
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return Orders
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
     * Set user
     *
     * @param \Jasdero\PassePlatBundle\Entity\User $user
     *
     * @return Orders
     */
    public function setUser(\Jasdero\PassePlatBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Jasdero\PassePlatBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Orders
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set state
     *
     * @param \Jasdero\PassePlatBundle\Entity\State $state
     *
     * @return Orders
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
     * Set source
     *
     * @param \Jasdero\PassePlatBundle\Entity\Source $source
     *
     * @return Orders
     */
    public function setSource(\Jasdero\PassePlatBundle\Entity\Source $source = null)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return \Jasdero\PassePlatBundle\Entity\Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Add product
     *
     * @param \Jasdero\PassePlatBundle\Entity\Product $product
     *
     * @return Orders
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


    /**
     * Set driveSynchro
     *
     * @param boolean $driveSynchro
     *
     * @return Orders
     */
    public function setDriveSynchro($driveSynchro)
    {
        $this->driveSynchro = $driveSynchro;

        return $this;
    }

    /**
     * Get driveSynchro
     *
     * @return boolean
     */
    public function getDriveSynchro()
    {
        return $this->driveSynchro;
    }


    /**
     * Add comment
     *
     * @param \Jasdero\PassePlatBundle\Entity\Comment $comment
     *
     * @return Orders
     */
    public function addComment(\Jasdero\PassePlatBundle\Entity\Comment $comment)
    {
        $comment->setOrder($this);
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
