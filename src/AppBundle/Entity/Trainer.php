<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trainer
 *
 * @ORM\Table(name="trainers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrainerRepository")
 */
class Trainer
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
     * @ORM\Column(name="firstName", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="login", type="string", length=255)
     */
    private $login;

    /**
     * @var string
     * @Assert\Email(message = "field.invalid_email")
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;    

    /**
     * @ORM\ManyToMany(targetEntity="Team", inversedBy="trainers")
     * @ORM\JoinTable(name="trainers_teams")
     */
    private $teams;    
    
    /**
     * @ORM\OneToMany(targetEntity="Training", mappedBy="trainer")
     */
    private $trainings;    

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="trainer")
     */
    private $tasks;
    
    public function __construct() {
        $this->teams = new ArrayCollection();
        $this->trainings = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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
     * @return Trainer
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
     * @return Trainer
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
     * Set login
     *
     * @param string $login
     *
     * @return Trainer
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Trainer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Trainer
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get teams
     *
     * @return Team
     */
    public function getTeams()
    {
        return $this->teams;
    }    
    
    /**
     * Add team
     *
     * @param Team $team
     *
     * @return Trainer
     */    
    public function addTeam(Team $team)
    {
        $this->teams->add($team);
        
        return $this;
    }

    /**
     * Remove team
     *
     * @return Trainer
     */    
    public function removeTeam(Team $team)
    {
        $this->teams->removeElement($team);
        
        return $this;
    }    

    /**
     * Get training
     *
     * @return Training
     */
    public function getTrainings()
    {
        return $this->trainings;
    }    
    
    /**
     * Add training
     *
     * @param Training $training
     *
     * @return Trainer
     */    
    public function addTraining(Training $training)
    {
        $this->trainings->add($training);
        
        return $this;
    }

    /**
     * Remove training
     *
     * @return Trainer
     */    
    public function removeTraining(Training $training)
    {
        $this->trainings->removeElement($training);
        
        return $this;
    } 

    /**
     * Get task
     *
     * @return Task
     */
    public function getTasks()
    {
        return $this->tasks;
    }    
    
    /**
     * Add task
     *
     * @param Task $task
     *
     * @return Trainer
     */    
    public function addTask(Task $task)
    {
        $this->tasks->add($task);
        
        return $this;
    }

    /**
     * Remove task
     *
     * @return Trainer
     */    
    public function removeTask(Task $task)
    {
        $this->tasks->removeElement($task);
        
        return $this;
    } 
    
}
