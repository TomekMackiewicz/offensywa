<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 * @UniqueEntity(
 *     fields={"year", "name"},
 *     message="year.name.already.in.use"
 * )
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
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(message = "field.not_blank")
     * @Assert\Regex(
     *   pattern = "^[12][0-9]{3}$^",
     *   match = true,
     *   message = "field.year"
     * )
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;

    /**
     * @var boolean
     * 
     * @ORM\Column(name="is_my", type="boolean", nullable=true)
     */
    private $isMy;

    /**
     * @var boolean
     * 
     * @ORM\Column(name="plays_league", type="boolean", nullable=true)
     */
    private $playsLeague;    
    
    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\File", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="logo", referencedColumnName="id", onDelete="CASCADE")
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
     * @ORM\ManyToMany(targetEntity="Training", mappedBy="teams", cascade={"persist"})
     */
    private $trainings; 

    /**
     * @ORM\ManyToMany(targetEntity="Trainer", mappedBy="teams")
     */
    private $trainers;
    
    public function __construct() {
        $this->players = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->homeGames = new ArrayCollection();
        $this->awayGames = new ArrayCollection();
        $this->trainings = new ArrayCollection();
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
     * Set team plays league
     *
     * @param bool $playsLeague
     *
     * @return Team
     */
    public function setPlaysLeague($playsLeague)
    {
        $this->playsLeague = $playsLeague;

        return $this;
    }

    /**
     * Get team plays League
     *
     * @return bool
     */
    public function getPlaysLeague()
    {
        return $this->playsLeague;
    }    
    
    /**
     * Set logo
     *
     * @param \AppBundle\Entity\File $logo
     *
     * @return Team
     */
    public function setLogo(\AppBundle\Entity\File $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return \AppBundle\Entity\File
     */
    public function getLogo()
    {
        return $this->logo;
    }

    public function nullLogo()
    {
        $this->logo = null;
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
     * Get trainings
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
     * Get trainers
     *
     * @return Trainer
     */
    public function getTrainers()
    {
        return $this->trainers;
    }    
    
    /**
     * Add trainer
     *
     * @param Trainer $rainer
     *
     * @return Team
     */    
    public function addTrainer(Trainer $trainer)
    {
        $this->trainers->add($trainer);
        
        return $this;
    }

    /**
     * Remove trainer
     *
     * @return Team
     */    
    public function removeTrainer(Trainer $trainer)
    {
        $this->trainers->removeElement($trainer);
        
        return $this;
    }    
    
}

