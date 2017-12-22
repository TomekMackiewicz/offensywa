<?php

namespace Application\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;

class Media extends BaseMedia
{
    /**
     * @var int $id
     */
    protected $id;    

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
