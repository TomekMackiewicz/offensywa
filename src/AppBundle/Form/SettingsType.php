<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SettingsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('seasonStart', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM',
                'attr' => [
                    'class' => 'form-control input-inline datemonthpicker',
                    'data-provide' => 'datemonthpicker',
                    'data-date-format' => 'dd-MM'
                ],
                'label' => 'season.start'
            ]) 
            ->add('seasonEnd', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM',
                'attr' => [
                    'class' => 'form-control input-inline datemonthpicker',
                    'data-provide' => 'datemonthpicker',
                    'data-date-format' => 'dd-MM'
                ],
                'label' => 'season.end'
            ]);                
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Settings'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_settings';
    }


}
