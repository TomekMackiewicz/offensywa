<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"player"},
 *     message="player.already.in.use"
 * )
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message = "field.not_blank")
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message = "field.not_blank")
     */
    protected $lastName;    

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message = "field.not_blank")
     * @Assert\Regex(
     *   pattern = "^[12][0-9]{3}$^",
     *   match = true,
     *   message = "field.year"
     * )
     */
    protected $year;
    
    /**
     * @Assert\Length(
     *     min=8,
     *     max=100,
     *     minMessage="user.password.short",
     *     groups={"Profile", "ResetPassword", "Registration", "ChangePassword"}
     * )
     * @Assert\Regex(
     *     pattern="/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).{8,100}$/",
     *     message="user.password.difficulty",
     *     groups={"Profile", "ResetPassword", "Registration", "ChangePassword"}
     * )
     */
    protected $plainPassword;    
    
    /**
     * @ORM\OneToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;    

    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="user")
     */
    private $requests;     
  
    public function __construct()
    {
        parent::__construct();
        $this->requests = new ArrayCollection();
    }

    /**
    * Set firstName
    *
    * @param string $firstName
    *
    * @return User
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
    * @return User
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
    * Set year
    *
    * @param string $year
    *
    * @return User
    */ 
    public function setYear($year)
    {
       $this->year = $year;
       
       return $this;
    }
 
    /**
    * Get year
    *
    * @return string
    */ 
    public function getYear()
    {
       return $this->year;
    }    
    
    /**
     * Set player
     *
     * @param Player $player
     *
     * @return User
     */
    public function setPlayer($player)
    {
        $this->player = $player;

        return $this;
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
     * Get requests
     *
     * @return Request
     */
    public function getRequests()
    {
        return $this->requests;
    }    
    
    /**
     * Add request
     *
     * @param Request $request
     *
     * @return User
     */    
    public function addRequest(Request $request)
    {
        $this->requests->add($request);
        
        return $this;
    }

    /**
     * Remove request
     *
     * @return User
     */    
    public function removeRequest(Request $request)
    {
        $this->requests->removeElement($request);
        
        return $this;
    } 
    
}    
