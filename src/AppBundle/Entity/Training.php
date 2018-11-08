<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Training
 *
 * @ORM\Table(name="training")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrainingRepository")
 * @UniqueEntity(
 *  fields={"startHour", "day", "location"},
 *  message="duplicate.training.date"
 * )
 */
class Training
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
     * @var integer
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="day", type="integer")
     */
    private $day;    
    
    /**
     * @var \Time
     * @Assert\NotBlank(message = "field.not_blank")
     * @Assert\Time(message = "field.invalid_date")
     * @ORM\Column(name="start_hour", type="time")
     */
    private $startHour;

    /**
     * @var \Time
     * @Assert\NotBlank(message = "field.not_blank")
     * @Assert\Time(message = "field.invalid_date")
     * @ORM\Column(name="end_hour", type="time")
     */
    private $endHour;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @Assert\NotBlank(message = "field.not_blank")
     * 
     * @ORM\ManyToMany(targetEntity="Team", inversedBy="trainings")
     * @ORM\JoinTable(name="training_team")
     */
    private $teams;    
    
    /**
     * @Assert\NotBlank(message = "field.not_blank")
     * 
     * @ORM\ManyToMany(targetEntity="Trainer", inversedBy="trainings")
     * @ORM\JoinTable(name="training_trainer")
     */
    private $trainers;    

    public function __construct() {
        $this->teams = new ArrayCollection();
        $this->trainers = new ArrayCollection();
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
     * Set day
     *
     * @param string $day
     *
     * @return Training
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }    
    
    /**
     * Set start hour
     *
     * @param \Time $startHour
     *
     * @return Training
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;

        return $this;
    }

    /**
     * Get start hour
     *
     * @return \Time
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     * Set end hour
     *
     * @param \Time $endHour
     *
     * @return Training
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;

        return $this;
    }

    /**
     * Get end hour
     *
     * @return \Time
     */
    public function getEndHour()
    {
        return $this->endHour;
    }    
    
    /**
     * Set location
     *
     * @param string $location
     *
     * @return Training
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
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
     * @return Training
     */    
    public function addTeam(Team $team)
    {
        $this->teams->add($team);
        
        return $this;
    }

    /**
     * Remove team
     *
     * @return Training
     */    
    public function removeTeam(Team $team)
    {
        $this->teams->removeElement($team);
        
        return $this;
    }    
    
    /**
     * Get trainers
     *
     * @return Trainer
     */
    public function getTrainers()
    {
        return $this->trainers;
    }

    /**
     * Add Trainer
     *
     * @param Trainer $trainer
     *
     * @return Training
     */    
    public function addTrainer(Trainer $trainer)
    {
        $this->trainers->add($trainer);
        
        return $this;
    }
 
    /**
     * Remove trainer
     *
     * @return Trainer
     */    
    public function removeTrainer(Trainer $trainer)
    {
        $this->trainers->removeElement($trainer);
        
        return $this;
    }     
    
}

