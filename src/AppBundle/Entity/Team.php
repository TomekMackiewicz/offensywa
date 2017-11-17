<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 */
class Team
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
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;

    /**
     * @var boolean
     * 
     * @ORM\Column(name="is_my", type="boolean")
     */
    private $isMy;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="logo", referencedColumnName="id")
     * })
     */
   private $logo; 
    
    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="team")
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="team")
     */
    private $games;    

    /**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="homeTeam")
     */
    private $homeGames; 

    /**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="awayTeam")
     */
    private $awayGames;     

    /**
     * @ORM\ManyToOne(targetEntity="Trainer", inversedBy="teams")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     */
    private $trainer;

    /**
     * @ORM\OneToMany(targetEntity="Training", mappedBy="team")
     */
    private $trainings; 

    /**
     * @ORM\OneToOne(targetEntity="Task", mappedBy="team")
     */
    private $task;
    
    public function __construct() {
        $this->players = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->homeGames = new ArrayCollection();
        $this->awayGames = new ArrayCollection();
        $this->trainings = new ArrayCollection();
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
     * @return Team
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
     * Set year
     *
     * @param string $year
     *
     * @return Team
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
     * Set my team
     *
     * @param bool $isMy
     *
     * @return Team
     */
    public function setIsMy($isMy)
    {
        $this->isMy = $isMy;

        return $this;
    }

    /**
     * Get is my team
     *
     * @return bool
     */
    public function getIsMy()
    {
        return $this->isMy;
    }

    /**
     * Set logo
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $logo
     *
     * @return Player
     */
    public function setLogo(\Application\Sonata\MediaBundle\Entity\Media $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getLogo()
    {
        return $this->logo;
    }
    
    /**
     * Get players
     *
     * @return array
     */
    public function getPlayers()
    {
        return $this->players;
    }
    
//    public function addPlayer(Category $category)
//    {
//        $this->categories->add($category);
//    }
//
//    public function removeCategory(Category $category)
//    {
//        $this->categories->removeElement($category);
//    }

    /**
     * Get games
     *
     * @return array
     */
    public function getGames()
    {
        return $this->games;
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
     * @return Team
     */    
    public function setTrainer(Trainer $trainer)
    {
        $this->trainer = $trainer;
        
        return $this;
    }

    /**
     * Set training
     *
     * @param Training $training
     *
     * @return Team
     */
    public function setTraining($training)
    {
        $this->training = $training;

        return $this;
    }

    /**
     * Get training
     *
     * @return Training
     */
    public function getTraining()
    {
        return $this->training;
    }    
    
    /**
     * Add training
     *
     * @param Training $training
     *
     * @return Team
     */    
    public function addTraining(Training $training)
    {
        $this->trainings->add($training);
        
        return $this;
    }

    /**
     * Remove training
     *
     * @return Team
     */    
    public function removeTraining(Training $training)
    {
        $this->trainings->removeElement($training);
        
        return $this;
    } 

    /**
     * Set task
     *
     * @param Task $task
     *
     * @return Team
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    } 
    
}

