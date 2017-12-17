<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @UniqueEntity(
 *     fields={"page"},
 *     message="category.already.assigned"
 * )
 */
class Category
{
    
    const NEWS = 1;
    const GALLERY = 2;
    
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var int
     * @ORM\Column(name="page", type="integer", nullable=true, unique=true)
     */
    private $page;    
    
    /**
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="categories")
     */
    private $posts;

    public function __construct() {
        $this->posts = new ArrayCollection();
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
     * @return Category
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
     * Set page
     *
     * @param int $page
     *
     * @return Category
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Get posts
     *
     * @return Post
     */
    public function getPosts()
    {
        return $this->posts;
    }    
}

