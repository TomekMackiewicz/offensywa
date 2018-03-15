<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
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
     * @ORM\Column(name="seasonStart", type="date")
     */
    private $seasonStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="seasonEnd", type="date")
     */
    private $seasonEnd;

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
     * Set seasonStart
     *
     * @param \DateTime $seasonStart
     *
     * @return Settings
     */
    public function setSeasonStart($seasonStart)
    {       
        $this->seasonStart = $seasonStart;

        return $this;
    }

    /**
     * Get seasonStart
     *
     * @return \DateTime
     */
    public function getSeasonStart()
    {
        return $this->seasonStart;
    }

    /**
     * Set seasonEnd
     *
     * @param \DateTime $seasonEnd
     *
     * @return Settings
     */
    public function setSeasonEnd($seasonEnd)
    {
        $this->seasonEnd = $seasonEnd;

        return $this;
    }

    /**
     * Get seasonEnd
     *
     * @return \DateTime
     */
    public function getSeasonEnd()
    {
        return $this->seasonEnd;
    }
}

