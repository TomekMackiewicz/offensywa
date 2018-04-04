<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class TeamType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'name'
            ))
            ->add('year', null, array(
                'label' => 'year'
            ))
            ->add('isMy', CheckboxType::class, array(
                'label'    => 'isMyTeam',
                'required' => false,
            ))
            ->add('playsLeague', CheckboxType::class, array(
                'label'    => 'playsLeague',
                'required' => false,
            ))                
            ->add('logo', HiddenType::class, array( 
                'data_class' => null,
                'mapped' => false,
                'label' => 'logo',
                'attr' => array('data-toggle' => 'modal', 'data-target' => '#fileButtonModal')
            ));                
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Team'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_team';
    }


}
