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
    
    public function __construct() {
        $this->players = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->homeGames = new ArrayCollection();
        $this->awayGames = new ArrayCollection();
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
    
}

