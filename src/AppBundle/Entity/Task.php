<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskRepository")
 */
class Task
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="trainerPresence", type="boolean", nullable=true)
     */
    private $trainerPresence;

    /**
     * @var bool
     *
     * @ORM\Column(name="attendanceList", type="boolean", nullable=true)
     */
    private $attendanceList;

    /**
     * @var bool
     *
     * @ORM\Column(name="outline", type="boolean", nullable=true)
     */
    private $outline;

    /**
     * @ORM\ManyToOne(targetEntity="Trainer", inversedBy="trainings")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     */
    private $trainer;    

    /**
     * @ORM\OneToOne(targetEntity="Team", inversedBy="task")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;   
    
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Task
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
     * Set trainerPresence
     *
     * @param boolean $trainerPresence
     *
     * @return Task
     */
    public function setTrainerPresence($trainerPresence)
    {
        $this->trainerPresence = $trainerPresence;

        return $this;
    }

    /**
     * Get trainerPresence
     *
     * @return bool
     */
    public function getTrainerPresence()
    {
        return $this->trainerPresence;
    }

    /**
     * Set attendanceList
     *
     * @param boolean $attendanceList
     *
     * @return Task
     */
    public function setAttendanceList($attendanceList)
    {
        $this->attendanceList = $attendanceList;

        return $this;
    }

    /**
     * Get attendanceList
     *
     * @return bool
     */
    public function getAttendanceList()
    {
        return $this->attendanceList;
    }

    /**
     * Set outline
     *
     * @param boolean $outline
     *
     * @return Task
     */
    public function setOutline($outline)
    {
        $this->outline = $outline;

        return $this;
    }

    /**
     * Get outline
     *
     * @return bool
     */
    public function getOutline()
    {
        return $this->outline;
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
     * @return Task
     */    
    public function setTrainer(Trainer $trainer)
    {
        $this->trainer = $trainer;
        
        return $this;
    }    

    /**
     * Set team
     *
     * @param Team $team
     *
     * @return Task
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
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
    
}

