<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @UniqueEntity(
 *     fields={"slug"},
 *     message="slug.already.assigned"
 * )
 */
class Post
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank(message = "field.not_blank")
     * @Assert\Regex(
     *   pattern = "/^[a-z0-9]+(?:-[a-z0-9]+)*$/",
     *   match = true,
     *   message = "field.regex"
     * )
     * @ORM\Column(name="slug", type="string", unique=true)
     */
    private $slug;    
    
    /**
     * @var string
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="body", type="text")
     */
    private $body;   
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publish_date", type="datetime", nullable=true)
     */
    private $publishDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modify_date", type="datetime", nullable=true)
     */
    private $modifyDate;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinTable(name="posts_categories")
     */
    private $categories;

    /**
     * @var File
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\File")
     * @ORM\JoinColumn(name="mainImage", referencedColumnName="id", onDelete="SET NULL")
     */
    private $mainImage;     
    
    /**
     * @ORM\ManyToMany(targetEntity="File")
     * @ORM\JoinTable(name="posts_images",
     *   joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="cascade")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="cascade")}
     * )
     */
    private $images;     
    
    public function __construct() {
        $this->categories = new ArrayCollection();
        $this->images = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }    
    
    /**
     * Set body
     *
     * @param string $body
     *
     * @return Post
     */
    public function setBody($body)
    {       
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }    
    
    /**
     * Set publishDate
     *
     * @param \DateTime $publishDate
     *
     * @return Post
     */
    public function setPublishDate($publishDate)
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    /**
     * Get publishDate
     *
     * @return \DateTime
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * Set modifyDate
     *
     * @param \DateTime $modifyDate
     *
     * @return Post
     */
    public function setModifyDate($modifyDate)
    {
        $this->modifyDate = $modifyDate;

        return $this;
    }

    /**
     * Get modifyDate
     *
     * @return \DateTime
     */
    public function getModifyDate()
    {
        return $this->modifyDate;
    }

    /**
     * Get categories
     *
     * @return Category
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    public function addCategory(Category $category)
    {        
        $this->categories->add($category);
    }

    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    } 

    /**
     * Set main image
     *
     * @param \AppBundle\Entity\File $mainImage
     *
     * @return Player
     */
    public function setMainImage(\AppBundle\Entity\File $mainImage = null)
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\File
     */
    public function getMainImage()
    {
        return $this->mainImage;
    }
    
    /**
     * Get images
     *
     * @return Image
     */
    public function getImages()
    {
        return $this->images;
    }
    
    public function addImage(File $image)
    {
        $this->images->add($image);
    }

    public function removeImage(File $image)
    {
        $this->images->removeElement($image);
    }
    
}

