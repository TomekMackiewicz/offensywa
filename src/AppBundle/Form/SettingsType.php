<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SettingsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('useSeason', CheckboxType::class, array(
                'label'    => 'season.use',
                'required' => false,
            ))                
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
            ])
            ->add('maxFileSize', ChoiceType::class, array(
                'choices'  => array(
                    '256 KB' => 262144,
                    '512 KB' => 524288,
                    '1 MB' => 1048576,
                    '2 MB' => 2097152,
                    '4 MB' => 4194304
                ),
                'label' => 'file.max_size'
            ));                
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
