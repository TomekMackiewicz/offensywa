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
    protected $first_name;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message = "field.not_blank")
     */
    protected $last_name;    

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
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
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
       $this->first_name = $firstName;
       
       return $this;
    }
 
    /**
    * Get firstName
    *
    * @return string
    */ 
    public function getFirstName() 
    {
       return $this->first_name;
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
       $this->last_name = $lastName;
       
       return $this;
    }
 
    /**
    * Get lastName
    *
    * @return string
    */ 
    public function getLastName()
    {
       return $this->last_name;
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
    
}    
