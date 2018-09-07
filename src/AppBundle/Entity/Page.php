<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Page
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @UniqueEntity(
 *     fields={"isAboutPage", "isContactPage"},
 *     message="This field is already in use."
 * )
 * @UniqueEntity(fields = "isAboutPage")
 * @UniqueEntity(fields = "isContactPage")
 */
class Page
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
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
     * @ORM\Column(name="slug", type="string")
     */
    private $slug;     
    
    /**
     * @var string
     * @Assert\NotBlank(message = "field.not_blank")
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var boolean
     * 
     * @ORM\Column(name="contact_page", type="boolean", nullable=true)
     */
    private $isContactPage;

    /**
     * @var boolean
     * 
     * @ORM\Column(name="about_page", type="boolean", nullable=true)
     */
    private $isAboutPage;    
    

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
     * @return Page
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
     * @return Page
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
     * @return Page
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
     * Set about page
     *
     * @param bool $isAboutPage
     *
     * @return Page
     */
    public function setIsAboutPage($isAboutPage)
    {
        $this->isAboutPage = $isAboutPage;

        return $this;
    }

    /**
     * Get about page
     *
     * @return bool
     */
    public function getIsAboutPage()
    {
        return $this->isAboutPage;
    }

    /**
     * Set contact page
     *
     * @param bool $isContactPage
     *
     * @return Page
     */
    public function setIsContactPage($isContactPage)
    {
        $this->isContactPage = $isContactPage;

        return $this;
    }

    /**
     * Get contact page
     *
     * @return bool
     */
    public function getIsContactPage()
    {
        return $this->isContactPage;
    }     
}

