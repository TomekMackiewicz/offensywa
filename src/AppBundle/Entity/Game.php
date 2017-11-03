<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
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
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @var int
     *
     * @ORM\Column(name="home_team_score", type="integer", nullable=true)
     */
    private $homeTeamScore;

    /**
     * @var int
     *
     * @ORM\Column(name="away_team_score", type="integer", nullable=true)
     */
    private $awayTeamScore;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="games")
     * @ORM\JoinColumn(name="home_team", referencedColumnName="id")
     */
    private $homeTeam;    

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="games")
     * @ORM\JoinColumn(name="away_team", referencedColumnName="id")
     */
    private $awayTeam;    

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
     * @return Game
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
     * Set location
     *
     * @param string $location
     *
     * @return Game
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
     * Set homeTeam
     *
     * @param int $homeTeamId
     *
     * @return Game
     */
    public function setHomeTeamId($homeTeamId)
    {
        $this->homeTeamId = $homeTeamId;

        return $this;
    }

    /**
     * Get homeTeamId
     *
     * @return int
     */
    public function getHomeTeamId()
    {
        return $this->homeTeamId;
    }

    /**
     * Set awayTeamId
     *
     * @param int $awayTeamId
     *
     * @return Game
     */
    public function setAwayTeamId($awayTeamId)
    {
        $this->awayTeamId = $awayTeamId;

        return $this;
    }

    /**
     * Get awayTeamId
     *
     * @return int
     */
    public function getAwayTeamId()
    {
        return $this->awayTeamId;
    }

    /**
     * Set homeTeamScore
     *
     * @param integer $homeTeamScore
     *
     * @return Game
     */
    public function setHomeTeamScore($homeTeamScore)
    {
        $this->homeTeamScore = $homeTeamScore;

        return $this;
    }

    /**
     * Get homeTeamScore
     *
     * @return int
     */
    public function getHomeTeamScore()
    {
        return $this->homeTeamScore;
    }

    /**
     * Set awayTeamScore
     *
     * @param integer $awayTeamScore
     *
     * @return Game
     */
    public function setAwayTeamScore($awayTeamScore)
    {
        $this->awayTeamScore = $awayTeamScore;

        return $this;
    }

    /**
     * Get awayTeamScore
     *
     * @return int
     */
    public function getAwayTeamScore()
    {
        return $this->awayTeamScore;
    }
    
    /**
     * Get home team
     *
     * @return Team
     */
    public function getHomeTeam()
    {
        return $this->homeTeam;
    }

    /**
     * Set homeTeam
     *
     * @param Team $homeTeam
     *
     * @return Game
     */     
    public function setHomeTeam(Team $homeTeam)
    {
        $this->homeTeam = $homeTeam;
        
        return $this;
    }    

    /**
     * Get away team
     *
     * @return Team
     */
    public function getAwayTeam()
    {
        return $this->awayTeam;
    }

    /**
     * Set awayTeam
     *
     * @param Team $awayTeam
     *
     * @return Game
     */     
    public function setAwayTeam(Team $awayTeam)
    {
        $this->awayTeam = $awayTeam;
        
        return $this;
    } 
    
}

