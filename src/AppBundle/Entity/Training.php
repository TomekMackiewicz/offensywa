<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Training
 *
 * @ORM\Table(name="training")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrainingRepository")
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
     * @var string
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="day", type="string")
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
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="trainings")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;    

    /**
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\ManyToOne(targetEntity="Trainer", inversedBy="trainings")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     */
    private $trainer;    
    
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
     * @param Team $team
     *
     * @return Training
     */    
    public function setTeam(Team $team)
    {
        $this->team = $team;
        
        return $this;
    }    

    /**
     * Get trainer
     *
     * @return Trainer
     */
    public function getTrainer()
    {
        return $this->trainer;
    }

    /**
     * Set Trainer
     *
     * @param Trainer $trainer
     *
     * @return Training
     */    
    public function setTrainer(Trainer $trainer)
    {
        $this->trainer = $trainer;
        
        return $this;
    }
    
}

