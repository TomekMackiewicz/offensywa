<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentRepository")
 * @UniqueEntity(
 *     fields={"player", "period", "paymentCategory"},
 *     message="player.already.paid"
 * )
 */
class Payment
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
     * @var int
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=255, nullable=true)
     */
    private $method;   
    
    /**
     * @var PaymentCategory
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\ManyToOne(targetEntity="PaymentCategory", inversedBy="payments")
     * @ORM\JoinColumn(name="payment_category", referencedColumnName="id")
     */
    private $paymentCategory;

    /**
     * @var \DateTime
     * @Assert\DateTime(message = "field.invalid_date")
     * @ORM\Column(name="period", type="date")
     */
    private $period;

    /**
     * @var \DateTime
     * @Assert\DateTime(message = "field.invalid_date")
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="payments")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;    

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
     * Set method
     *
     * @param string $method
     *
     * @return Payment
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set paymentCategory
     *
     * @param string $paymentCategory
     *
     * @return Payment
     */
    public function setPaymentCategory($paymentCategory)
    {
        $this->paymentCategory = $paymentCategory;

        return $this;
    }

    /**
     * Get paymentCategory
     *
     * @return string
     */
    public function getPaymentCategory()
    {
        return $this->paymentCategory;
    }

    /**
     * Set period
     *
     * @param \DateTime $period
     *
     * @return Payment
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return \DateTime
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Payment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set Player
     *
     * @param Player $player
     *
     * @return Payment
     */    
    public function setPlayer(Player $player)
    {
        $this->player = $player;
        
        return $this;
    }    
    
}

