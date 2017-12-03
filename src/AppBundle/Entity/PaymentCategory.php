<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PaymentCategory
 *
 * @ORM\Table(name="payment_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentCategoryRepository")
 */
class PaymentCategory
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
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var int
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;    
    
    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="paymentCategory")
     */
    private $payments;    

    public function __construct() {
        $this->payments = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return PaymentCategory
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
     * Set amount
     *
     * @param integer $amount
     *
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
    
    /**
     * Get payments
     *
     * @return array
     */
    public function getPayments()
    {
        return $this->payments;
    }    
    
}

