<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => [
                    'class' => 'form-control input-inline datetimepicker',
                    'data-provide' => 'datetimepicker'
                ],
                'label' => 'Data'
            ])
            ->add('trainerPresence', null, array(
                'label' => 'Obecność trenera'
            ))
            ->add('attendanceList', null, array(
                'label' => 'Lista obecności'
            ))
            ->add('outline', null, array(
                'label' => 'Konspekt'
            ))
            ->add('trainer', EntityType::class, array(
                'class' => 'AppBundle:Trainer',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'label' => 'Trener'
            ))                
            ->add('team', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'label' => 'Drużyna'
            ));                
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Task'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_task';
    }


}
