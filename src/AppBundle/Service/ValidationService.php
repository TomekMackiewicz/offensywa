<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormError;

/**
 * Validation service
 */
class ValidationService extends Controller 
{
    
    public function __construct(Registry $doctrine, TranslatorInterface $translator)
    {
        $this->doctrine = $doctrine;
        $this->translator = $translator;
    }     

    /**
     * Validate image
     * 
     * @param object $image
     * @return FormError
     */
    public function validateImage($image) {
        $em = $this->doctrine->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->findFirst();
        $errors = [];
        
        // validate mime type
        if ($image->getType() != "image/jpeg" && $image->getType() != "image/png" && $image->getType() != "image/bmp") {
            $errors[] = new FormError($this->translator->trans('file.not_valid_type'));                    
        }

        // validate size
        $maxFileSize = $settings->getMaxFileSize();
        if ($image->getSize() > $maxFileSize) {
            $errors[] = new FormError($this->translator->trans('file.not_valid_size'));                    
        }
       
        if (!empty($errors)) {
            return $errors;
        } else {
            return null;
        }
       
    }
    
}

