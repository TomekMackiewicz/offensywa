<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

/**
 * Validation service
 */
class ValidationService extends Controller {

    /**
     * Validate image
     * 
     * @param object $image
     * @return FormError
     */
    public function validateImage($image) {
        $errors = [];
         
        // validate mime type
        if ($image->getType() != "image/jpeg" && $image->getType() != "image/png" && $image->getType() != "image/bmp") {
            $errors[] = new FormError("Not valid file type");                    
        }

        // validate size
        if ($image->getSize() > 100) {
            $errors[] = new FormError("Image is to big");                    
        }
       
        if (!empty($errors)) {
            return $errors;
        } else {
            return null;
        }
       
    }
    
}

