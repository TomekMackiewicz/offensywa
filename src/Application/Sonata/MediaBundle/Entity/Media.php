<?php

namespace Application\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;

class Media extends BaseMedia
{
    /**
     * @var int $id
     */
    protected $id;

//    /**
//      * @var Gallery
//      *
//      * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="images")
//      * @ORM\JoinColumns({
//      *     @ORM\JoinColumn(name="gallery", referencedColumnName="id")
//      * })
//      */
//    private $gallery;    
    
    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }
}
