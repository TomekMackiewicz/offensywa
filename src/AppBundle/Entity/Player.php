<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Player
 *
 * @ORM\Table(name="player")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class Player
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
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var \DateTime
     * @Assert\DateTime(message = "field.invalid_date")
     * @ORM\Column(name="birth_date", type="date", nullable=true)
     */
    private $birthDate;   

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255, nullable=true)
     */
    private $position;

    /**
     * @var string
     * @Assert\Regex(
     *   pattern = "/^(0|[1-9][0-9]*)$/",
     *   match = true,
     *   message = "field.invalid.phone"
     * ) 
     * @ORM\Column(name="parent_phone", type="string", length=255, nullable=true)
     */
    private $parentPhone;     
    
    /**
     * @var string
     * @Assert\Email(message = "field.invalid_email")
     * @ORM\Column(name="parent_email", type="string", length=255, nullable=true)
     */
    private $parentEmail;    
    
    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="image", referencedColumnName="id", onDelete="CASCADE")
     */
    private $image;    

    /**
     * @var Gallery
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Gallery", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="gallery", referencedColumnName="id", onDelete="CASCADE")
     */
    private $gallery;   
   
    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="players")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;  

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="player")
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Player
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Player
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Player
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

//    /**
//     * Set year
//     *
//     * @param string $year
//     *
//     * @return Player
//     */
//    public function setYear($year)
//    {
//        $this->year = $year;
//
//        return $this;
//    }
//
//    /**
//     * Get year
//     *
//     * @return string
//     */
//    public function getYear()
//    {
//        return $this->year;
//    }    
    
    /**
     * Set position
     *
     * @param string $position
     *
     * @return Player
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set parent email
     *
     * @param string $parentEmail
     *
     * @return Player
     */
    public function setParentEmail($parentEmail)
    {
        $this->parentEmail = $parentEmail;

        return $this;
    }

    /**
     * Get parent email
     *
     * @return string
     */
    public function getParentEmail()
    {
        return $this->parentEmail;
    }

    /**
     * Set parent phone
     *
     * @param string $parentPhone
     *
     * @return Player
     */
    public function setParentPhone($parentPhone)
    {
        $this->parentPhone = $parentPhone;

        return $this;
    }

    /**
     * Get parent phone
     *
     * @return string
     */
    public function getParentPhone()
    {
        return $this->parentPhone;
    }
    
    /**
     * Get team
     *
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set Team
     *
     * @param $team
     *
     * @return Player
     */    
    public function setTeam($team)
    {
        $this->team = $team;
        
        return $this;
    }
    
    /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     *
     * @return Player
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set gallery
     *
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $gallery
     *
     * @return Player
     */
    public function setGallery(\Application\Sonata\MediaBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery
     *
     * @return \Application\Sonata\MediaBundle\Entity\Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Get payments
     *
     * @return Payment
     */
    public function getPayments()
    {
        return $this->payments;
    }    
    
    /**
     * Add payment
     *
     * @param Payment $payment
     *
     * @return Player
     */    
    public function addPayment(payment $payment)
    {
        $this->payment->add($payment);
        
        return $this;
    }

    /**
     * Remove payment
     *
     * @return Player
     */    
    public function removePayment(Payment $payment)
    {
        $this->payments->removeElement($payment);
        
        return $this;
    } 
    
}
