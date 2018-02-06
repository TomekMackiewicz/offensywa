<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Util\LegacyFormHelper;
 
class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', null, array(
            'label' => 'firstName'
        ));
        $builder->add('lastName', null, array(
            'label' => 'lastName'
        ));
        $builder->add('year', null, array(
            'label' => 'year'
        ));      
    }
 
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }
 
    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
 
    public function getName()
    {
        return $this->getBlockPrefix();
    }
 
}

